import React, { Fragment, useState, useMemo } from "react";
import GeneralSettingPage from "../general-setting-page";
import NotificationSettingPage from "../notification-setting-page/notification-setting-page";
import UserSettingPage from "../user-setting-page";

const SettingPage = () => {
    const [ tab, setTab ] = useState('general');

    const content = useMemo(() => {
        switch (tab) {
            case 'general':
                return <GeneralSettingPage />;
            case 'notif':
                return <NotificationSettingPage />;
            case 'users':
                return <UserSettingPage />
            default:
                return <GeneralSettingPage />;
        }
    }, [tab]);

    return (
        <Fragment>
            <ul className="nav nav-tabs">
                <li role="presentation"
                    className={tab == 'general' ? 'active' : ''}
                    onClick={() => setTab('general')}
                ><a>General</a></li>
                <li role="presentation"
                    className={tab == 'notif' ? 'active' : ''}
                    onClick={() => setTab('notif')}
                ><a>Notification</a></li>
                <li role="presentation"
                    className={tab == 'users' ? 'active' : ''}
                    onClick={() => setTab('users')}
                ><a>Users</a></li>
            </ul>
            <div className="project-view-content">
                { content }
            </div>
        </Fragment>);
}

export default SettingPage;