import React, { useEffect, Fragment } from "react";
import { useSearchParams, useNavigate } from "react-router-dom";

import { connect} from "react-redux";
import { withStoreService } from '../hoc';
import { compose } from '../../utils';
import { fetchMessages } from "../../actions";

import './message-list.css';
import Spinner from "../spiner";
import ErrorIndicator from "../error-indicator";
import Pagination from "../pagination";
import ActiveFiltres from "../active-filtre";
import FilterInput from "../filter-input";

const MessageList = (props) => {
    const { messages, levels, fetchMessages } = props;
    const [ searchParams ] = useSearchParams();
    const navigate = useNavigate();

    const getLevelCodeById = (id) => {
        for (let i = 0; i < levels.data.length; i++) {
            if (levels.data[i].id == id) {
                return levels.data[i].key;
            }
        }
        return ;
    };

    useEffect(() => {
        fetchMessages();
    }, [searchParams]);

    if (messages.loading) {
        return (
            <Spinner />
        );
    }

    if (messages.error !== null) {
        return <ErrorIndicator/>
    }

    const onClickHandler = (id) => {
        const currentParams = Object.fromEntries([...searchParams]);

        const url = `/stat/item?filter.id=${id}&filter.date=${currentParams['filter.date']}`;
        navigate(url, { replace: true });
    }

    const messagesTr = messages.data.rows ? messages.data.rows.map((item) => (
        <tr className='message-item' key={item.id} onClick={() => onClickHandler(item.id)}>
            <th className='tr-id' scope="row">{item.id}</th>
            <td className='tr-level'>{getLevelCodeById(item.level_id)}</td>
            <td className='tr-message'><div className='tr-message-content'>{item.message}</div></td>
            <td className='tr-create'>{item.create}</td>
            <td className='tr-ip'>{item.ip}</td>
        </tr>)) : null;

    const pagination = messages.data.pagination ? <Pagination pagination={messages.data.pagination} /> : null;

    return (
        <Fragment>
            <div className="panel panel-default">
                <ActiveFiltres />
                <table className="table">
                    <thead>
                    <tr>
                        <th className='tr-id' >#Id</th>
                        <th className='tr-level'>Level code</th>
                        <th className='th-message'>Message</th>
                        <th className='tr-create'>Created</th>
                        <th className='tr-ip'>Ip</th>
                    </tr>
                    <tr>
                        <th className='tr-id' ><FilterInput code='id'/></th>
                        <th className='tr-level'><FilterInput code='level'/></th>
                        <th className='th-message'><FilterInput code='message'/></th>
                        <th className='tr-create'></th>
                        <th className='tr-ip'><FilterInput code='ip' /></th>
                    </tr>
                    </thead>
                    <tbody>
                        { messagesTr }
                    </tbody>
                </table>
            </div>
            { pagination }
        </Fragment>)
}


const mapStateToProps = ({ messages, levels }) => {
    return { messages, levels }
}

const mapDispatchToProps = (dispatch, ownProps) => {
    const  { datastoreService } = ownProps;
    return {
        fetchMessages: () => fetchMessages(datastoreService, dispatch)
    }
}

export default compose(
    withStoreService(),
    connect(mapStateToProps,mapDispatchToProps)
)(MessageList)