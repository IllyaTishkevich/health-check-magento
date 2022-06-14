import React, { Fragment, useEffect } from "react";
import './user-settings-page.css';
import { fetchUsers, fetchRemoveUser } from "../../actions";
import { compose } from "../../utils";
import { withStoreService } from "../../components/hoc";
import { connect } from "react-redux";
import Spinner from "../../components/spiner";
import ErrorIndicator from "../../components/error-indicator";
import AddUser from "../../components/add-user/add-user";

const UserSettingPage = (props) => {
    const { users, fetchUsers, fetchRemoveUser } = props;
    const { data, loading, error } = users;
    useEffect(() => {
        fetchUsers();
    }, []);

    if (loading) {
        return (
            <Spinner />
        );
    }

    if (error !== '') {
        return <ErrorIndicator message={error}/>
    }
    const emails = data ? data.map((item) => <div className='well users-email' key={'email'+item.id}>
        <span className='mail'>{item.email}</span>
        <button className='btn btn-warning' onClick={() => fetchRemoveUser(item.id, fetchUsers)}>
            <span className="glyphicon glyphicon-remove" aria-hidden="true">
            </span>
        </button>
    </div>) : null;
    return (
        <Fragment>
            <div className='emails-container'>
                { emails }
            </div>
            <AddUser reloadHandler={fetchUsers}/>
        </Fragment>);
}


const mapStateToProps = ({ users }) => {
    return { users }
}

const mapDispatchToProps = (dispatch, ownProps) => {
    const  { datastoreService } = ownProps;
    return {
        fetchUsers: () => fetchUsers(datastoreService, dispatch),
        fetchRemoveUser: (id, reloadHandler) => fetchRemoveUser(datastoreService, dispatch, id, reloadHandler)
    }
}

export default compose(
        withStoreService(),
        connect(mapStateToProps,mapDispatchToProps)
    )(UserSettingPage);