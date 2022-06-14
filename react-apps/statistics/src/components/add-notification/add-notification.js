import React, { useState } from "react";

import { fetchSaveNotification } from "../../actions";
import { compose } from "../../utils";
import { withStoreService } from "../hoc";
import { connect } from "react-redux";

const AddNotification = (props) => {
    const [ level, setLevel ] = useState();
    const [ modalView, setModalVies ] = useState(false);
    const { levels, fetchSaveNotification, reloadHandler } = props;

    const viewHandler = () => {
      setModalVies(!modalView);
    };

    const selectHandler = (e) => {
        const value = e.target.value;
        if (value) {
            setLevel(value);
        }
    };

    const addRowHandler = () => {
        if (level) {
            const row = {
                "level_id": level,
                "notification_id": "",
                "settings": "{}",
                "active": false
            };
            fetchSaveNotification(row, reloadHandler);
        }
    };
    const options = levels.data.map(option => <option key={option.id} value={option.id}>
        { option.key }
    </option>)
    const modal = modalView ? (
            <div className='add-notif-block'>
                <select className='form-control' value={level} onChange={selectHandler}>
                    <option value=''></option>
                    { options }
                </select>
                <button onClick={addRowHandler} className='btn btn-info'>Add</button>
            </div>) : null
    const plusButton = !modalView ? <button onClick={viewHandler} className='btn btn-info'>
        <span className="glyphicon glyphicon-plus" aria-hidden="true"></span></button> : null;
    return (
        <div>
            { modal }
            { plusButton }
        </div>);
}


const mapDispatchToProps = (dispatch, ownProps) => {
    const  { datastoreService } = ownProps;
    return {
        fetchSaveNotification: (data, reloadHandler) =>
            fetchSaveNotification(datastoreService, dispatch, data, reloadHandler),
    }
}

export default compose(
    withStoreService(),
    connect(mapDispatchToProps)
)( AddNotification );