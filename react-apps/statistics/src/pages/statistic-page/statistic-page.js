import React, { useEffect, Fragment, useState } from "react";
import { connect } from "react-redux";
import { withStoreService } from '../../components/hoc';
import { useSearchParams } from "react-router-dom";

import { fetchLevels } from "../../actions";
import { compose } from '../../utils';

import MessageList from "../../components/message-list";
import Chart from "../../components/chart";

import './statistick-page.css';

const StatisticPage = (props) => {
    const { levels, fetchLevels } = props;
    const [ timeFilterFrom, setTimeFilterFrom ] = useState();
    const [ timeFilterTo, setTimeFilterTo ] = useState();
    const [ searchParams, setSearchParams ] = useSearchParams();

    useEffect(() => {
        if (timeFilterFrom && timeFilterTo) {
            const currentParams = Object.fromEntries([...searchParams]);
            setSearchParams({ ...currentParams, 'filter.date': `${timeFilterFrom}_${timeFilterTo}`});
        } else {
            const currentParams = Object.fromEntries([...searchParams]);
            if (currentParams['filter.date'] == undefined) {
                const now = new Date();
                const timestampNow = now.getTime();
                const from = now.setDate(now.getDate() - 1);
                setTimeFilterTo(timestampNow);
                setTimeFilterFrom(from);
            }
        }

        if( levels.data.length === 0 ) {
            fetchLevels();
        }

    }, [levels.data.length, timeFilterFrom, timeFilterTo]);


    return  <Fragment>
                { levels.data.length ? <Chart levels={ levels.data } /> : null }
                <MessageList />
            </Fragment>

}

const mapStateToProps = ({ levels }) => {
    return { levels }
}

const mapDispatchToProps = (dispatch, ownProps) => {
    const  { datastoreService } = ownProps;
    return {
        fetchLevels: () => fetchLevels(datastoreService, dispatch),
    }
}

export default compose(
    withStoreService(),
    connect(mapStateToProps,mapDispatchToProps)
)(StatisticPage)