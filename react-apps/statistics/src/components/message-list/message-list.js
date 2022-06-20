import React, {useEffect, useRef, Fragment, useMemo} from "react";
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

    if (messages.error !== '') {
        return <ErrorIndicator message={messages.error}/>
    }

    const onClickHandler = (id) => {
        const currentParams = Object.fromEntries([...searchParams]);

        const url = `/stat/item?filter.id=${id}&filter.date=${currentParams['filter.date']}`;
        navigate(url, { replace: true });
    };

    const messagesTr = messages.data.rows ? messages.data.rows.map((item, index) => (
        <MessageItem item={item} key={index}
                     level={getLevelCodeById(item.level_id)}
                     onClickHandler={onClickHandler}
        />)) : null;

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
                        <th className='tr-message'>Message</th>
                        <th className='tr-create'>Created</th>
                        <th className='tr-ip'>Ip</th>
                    </tr>
                    <tr>
                        <th className='tr-id' ><FilterInput code='id'/></th>
                        <th className='tr-level'><FilterInput code='level'/></th>
                        <th className='tr-message'><FilterInput code='message'/></th>
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

const MessageItem = ({item, level, onClickHandler}) => (
        <tr className='message-item' key={item.id} onClick={() => onClickHandler(item.id)}>
            <th className='tr-id' scope="row">{item.id}</th>
            <td className='tr-level'>{level}</td>
            <td className='tr-message'>
                {item.message.substring(0, 149)}{item.message.length > 150 ? '...' : ''}
            </td>
            <td className='tr-create'>{item.create}</td>
            <td className='tr-ip'>{item.ip}</td>
        </tr>)


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