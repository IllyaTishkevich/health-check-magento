import React, { useState } from "react";
import { fetchAddUser } from "../../actions";
import { compose } from "../../utils";
import { withStoreService } from "../hoc";
import { connect } from "react-redux";

const AddUser = (props) => {
    const { fetchAddUser, reloadHandler } = props;
    const [ email, setEmail ] = useState('');
    const onChangeHandler = (e) => {
        setEmail(e.target.value);
    };

    const onSubmitHandler = () => {
        fetchAddUser(email, reloadHandler);
    };

    return (
        <div className='add-email'>
            <input type='email' value={email} onChange={onChangeHandler}/>
            <button className='btn btn-info' onClick={onSubmitHandler}>Add</button>
        </div>);
}

const mapDispatchToProps = (dispatch, ownProps) => {
    const  { datastoreService } = ownProps;
    return {
        fetchAddUser: (data, reloadHandler) =>
            fetchAddUser(datastoreService, dispatch, data, reloadHandler),
    }
}

export default compose(
    withStoreService(),
    connect(mapDispatchToProps)
)( AddUser );