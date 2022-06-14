import React, { Fragment, useEffect, useState } from "react";

import { fetchProject } from "../../actions";
import { compose } from "../../utils";
import { withStoreService } from "../../components/hoc";
import { connect } from "react-redux";
import Spinner from "../../components/spiner";
import ErrorIndicator from "../../components/error-indicator";

const GeneralSettingPage = ( props ) => {
    const { project, fetchProject } = props;
    const { data, loading, error } = project;
    useEffect(() => {
            fetchProject();
    }, []);

    if (loading) {
        return (
            <Spinner />
        );
    }

    if (error !== '') {
        return <ErrorIndicator message={error}/>
    }

    return (
        <Fragment>
            <p className="project-delete-button">
                <a className="btn btn-danger" href={`/project/delete?id=${data.id}`}
                   data-confirm="Are you sure you want to delete this item?" data-method="post">Delete
                </a>
            </p>
            <table id="w0" className="table table-striped table-bordered detail-view">
                <tbody>
                <tr>
                    <th>Name</th>
                    <td>{data.name}</td>
                </tr>
                <tr>
                    <th>Auth Key</th>
                    <td>{data.auth_key}</td>
                </tr>
                <tr>
                    <th>Project Url</th>
                    <td><a href={data.url}>{data.url}</a></td>
                </tr>
                <tr>
                    <th>Project Owner</th>
                    <td>{data.owner}</td>
                </tr>
                </tbody>
            </table>
        </Fragment>);
}

const mapStateToProps = ({ project }) => {
    return { project }
}

const mapDispatchToProps = (dispatch, ownProps) => {
    const  { datastoreService } = ownProps;
    return {
        fetchProject: () => fetchProject(datastoreService, dispatch),
    }
}

export default compose(
    withStoreService(),
    connect(mapStateToProps,mapDispatchToProps)
)(GeneralSettingPage)
