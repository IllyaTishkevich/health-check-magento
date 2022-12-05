import React, { useEffect, useState, useMemo } from "react";
import { useSearchParams } from "react-router-dom";

import { connect} from "react-redux";
import { withStoreService } from '../../components/hoc';
import { compose } from '../../utils';
import { fetchNotifications, fetchSenders, fetchAllLevels } from "../../actions";

import Spinner from "../../components/spiner";
import ErrorIndicator from "../../components/error-indicator";
import NotificationRow from "../../components/notification-row";
import AddNotification from "../../components/add-notification";

const NotificationSettingPage = (props) => {
    const [ rows, setRows ] = useState();
    const addRowsHandler = (row) => {
        const newRows = [ ...rows];
        newRows.push(row);
        setRows(newRows);
    };

    const {
        notification,
        senders,
        levels,
        fetchNotifications,
        fetchSenders,
        fetchAllLevels
    } = props;

    const { searchParams } = useSearchParams();

    const reloadNotifications = () => {
        fetchNotifications();
    };

    useEffect(() => {
        fetchNotifications();
        fetchSenders();
        fetchAllLevels();
    }, [ searchParams ]);

    useEffect(() => {
        if (notification.data && notification.data.row) {
            setRows(notification.data.row);
        }
    }, [ notification ]);

    if (notification.loading || senders.loading || levels.loading) {
        return (
            <Spinner />
        );
    }

    if (senders.error !== '' || notification.error !== ''|| levels.error !== '') {
        return <ErrorIndicator message={senders.error + notification.error}/>
    }

    const getLevelById = (id) => {
        for(let i = 0; i < levels.data.length; i++) {
            if (id == levels.data[i].id) {
                return levels.data[i];
            }
        }
    };

    if (levels.data.length == 0) {
        return null;
    }

    const notfications = rows ? rows.map((item, key) => {
        return <NotificationRow key={key} data={item}
                                senders={senders}
                                level={getLevelById(item.level_id)}
                                reloadHandler={reloadNotifications}
        />
    }) : null;
    return (
        <div>
            { notfications }
            <AddNotification levels={levels} reloadHandler={reloadNotifications}/>
        </div>)
}

const mapStateToProps = ({ notification, senders, levels }) => {
    return { notification, senders, levels }
}

const mapDispatchToProps = (dispatch, ownProps) => {
    const  { datastoreService } = ownProps;
    return {
        fetchNotifications: () => fetchNotifications(datastoreService, dispatch),
        fetchSenders: () => fetchSenders(datastoreService, dispatch),
        fetchAllLevels: () => fetchAllLevels(datastoreService, dispatch),
    }
}

export default compose(
    withStoreService(),
    connect(mapStateToProps,mapDispatchToProps)
)(NotificationSettingPage)

