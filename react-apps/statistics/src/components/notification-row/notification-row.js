import React, { useState, useMemo, useEffect, Fragment } from "react";
import Mail from "./mail";

import { connect} from "react-redux";
import { withStoreService } from '../hoc';
import { fetchSaveNotification, fetchRemoveNotification } from "../../actions";
import { compose } from '../../utils';

import './notifi.css';
import ToggleSwitcher from "../toggle-switcher";

const NotificationRow = (props) => {
    const { data, senders, level, fetchSaveNotification, fetchRemoveNotification, reloadHandler } = props;
    const [ notificationId, setNotificationId ] = useState(data.notification_id ? data.notification_id: '');
    const [ settings, setSettings ]
        = useState(data.settings ? data.settings : JSON.stringify({}));
    const [ active, setActive] = useState(data.active == 0 ? false : true);

    const sendData = () => {
        const sendData = data;
        sendData.id = data.id ;
        sendData.notification_id = notificationId;
        sendData.active = active;
        sendData.settings = settings;
        fetchSaveNotification(sendData);
    };

    const removeHandler = () => {
      fetchRemoveNotification(data.id, reloadHandler);
    };

    const onChangeNotification = (e) => {
        setNotificationId(e.target.value);
    };

    const setSettingHandler = (object) => {
        setSettings(JSON.stringify(object));
    };

    const onChangeActive = () => {
      setActive(!active);
    };

    useEffect(sendData, [active, settings, notificationId]);

    const options = senders.data.map((option) => <option key={option.id} value={option.id}>{option.name}</option>)

    const notifSetting = useMemo(() => {
        if (notificationId == '') {
            return null;
        }

        let currentNotif;
        for (let i = 0; i < senders.data.length; i++) {
            if (notificationId == senders.data[i].id) {
                currentNotif = senders.data[i];
                break;
            }
        }
        switch (currentNotif.name) {
            case ('SendMail') :
                return <Mail setting={settings} setSetting={setSettingHandler} name={level.key}/>
            default :
                return null;
        }
    }, [ notificationId ])

    if (data.id == undefined) {
        return null;
    }

    if (level == undefined) {
        return  null;
    }

    return (
        <div className='notif-row panel panel-default'>
            <div className='panel-heading'>
                <h3 className='notif-label'>{level.key}</h3>
            </div>
            <div className='notif-settings panel-body'>
                <div className='line-row'>
                    <div className='notif-set'>
                        <label htmlFor={`active-${level.key}`}>Active:</label>
                        <ToggleSwitcher value={active} handler={onChangeActive} id={`active-${level.key}`}/>
                    </div>
                    <div className='notif-set'>
                        <label htmlFor={`notif-${level.key}`}>Notification by:</label>
                        <select className='form-control'  value={notificationId} onChange={onChangeNotification} id={`notif-${level.key}`}>
                            <option value=''></option>
                            {options}
                        </select>
                    </div>
                    <button className='btn btn-warning' onClick={removeHandler}>
                        <span className="glyphicon glyphicon-remove" aria-hidden="true">
                        </span>
                    </button>
                </div>
                <div className='notif-set'>
                    { notifSetting }
                </div>
            </div>
        </div>)
}

const mapDispatchToProps = (dispatch, ownProps) => {
    const  { datastoreService } = ownProps;
    return {
        fetchSaveNotification: (data, reloadHandler) => fetchSaveNotification(datastoreService, dispatch, data, reloadHandler),
        fetchRemoveNotification: (id, reloadHandler) => fetchRemoveNotification(datastoreService, dispatch, id, reloadHandler)
    }
}

export default compose(
        withStoreService(),
        connect(mapDispatchToProps)
    )(NotificationRow);
