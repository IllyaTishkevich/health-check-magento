import React, { useEffect, Fragment } from "react";
import { useSearchParams } from "react-router-dom";

import { connect} from "react-redux";
import { withStoreService } from '../../components/hoc';
import { compose } from '../../utils';
import { fetchMessages, fetchLevels } from "../../actions";

import Spinner from "../../components/spiner";
import ErrorIndicator from "../../components/error-indicator";
import ItemMessage from "../../components/item-message";
import Chart from "../../components/chart/chart";

const ItemPage = (props) => {
    const { messages, levels, fetchMessages, fetchLevels } = props;
    const [ searchParams ] = useSearchParams();

    useEffect(() => {
            fetchMessages();
            fetchLevels();
    }, [searchParams]);

    if (messages.loading || levels.loading) {
        return (
            <Spinner />
        );
    }

    if (messages.error !== null || levels.error !== null) {
        return <ErrorIndicator/>
    }
    const item = messages.data.rows[0];
    const thisLevel = levels.data.filter((level) => item.level_id === level.id);

    return  <Fragment>
                <Chart levels={thisLevel} messageId={item.id}/>
                <ItemMessage key={item.id} data={item} level={thisLevel[0]}/>
            </Fragment>
}


const mapStateToProps = ({ messages, levels }) => {
    return { messages, levels }
}

const mapDispatchToProps = (dispatch, ownProps) => {
    const  { datastoreService } = ownProps;
    return {
        fetchMessages: () => fetchMessages(datastoreService, dispatch),
        fetchLevels: () => fetchLevels(datastoreService, dispatch),
    }
}

export default compose(
    withStoreService(),
    connect(mapStateToProps,mapDispatchToProps)
)(ItemPage)
