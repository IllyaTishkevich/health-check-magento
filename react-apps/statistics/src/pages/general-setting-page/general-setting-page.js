import React, { Fragment, useEffect, useState } from "react";

import { fetchProject, fetchSetSetting } from "../../actions";
import { compose } from "../../utils";
import { withStoreService } from "../../components/hoc";
import { connect } from "react-redux";
import Spinner from "../../components/spiner";
import ErrorIndicator from "../../components/error-indicator";

import "./general-setting-page.css";
import ToggleSwitcher from "../../components/toggle-switcher";

const GeneralSettingPage = ( props ) => {
    const {
        project,
        fetchProject,
        fetchSetGmt,
        fetchSetEnableServerCheck,
        fetchSetMessageFilter,
        fetchSetArchivingPeriod
    } = props;
    const { data, loading, error } = project;

    const [messageFilter, setMessageFilter] = useState(data.message_filter || '');
    const [archivingPeriod, setArchivingPeriod] = useState(data.archiving_period || 7);

    useEffect(() => {
            fetchProject();
    }, []);

    useEffect(() => {
        if (loading) {
            return;
        }
        if (data.message_filter) {
            setMessageFilter(data.message_filter);
        }

        if (data.archiving_period) {
            setArchivingPeriod(data.archiving_period || 7);
        }
    }, [data]);

    if (loading) {
        return (
            <Spinner />
        );
    }

    if (error !== '') {
        return <ErrorIndicator message={error}/>
    }

    const archivingPeriodOnChange = (e) => {
        setArchivingPeriod(e.target.value)
    }

    const archivingPeriodOnBlur = () => {
        fetchSetArchivingPeriod(archivingPeriod, fetchProject);
    }

    const messageFilterOnChange = (e) => {
        setMessageFilter(e.target.value)
    }

    const messageFilterOnBlur = () => {
        fetchSetMessageFilter(messageFilter, fetchProject);
    }

    const setGmt = (e) => {
        const value = e.target.value;
        fetchSetGmt(value, fetchProject);
    }

    const setActiveChecker = (value) => {
        fetchSetEnableServerCheck(value, fetchProject);
    }

    return (
        <Fragment>
            <p className="project-delete-button">
                <a className="btn btn-danger" href={`/project/delete?id=${data.id}`}
                   data-confirm="Are you sure you want to delete this item?" data-method="post">Delete
                </a>
            </p>
            <div className='general-setting-table'>
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
                <div className='selected-settings'>
                    <div className='row-set'>
                        <span className='setting-label'>Enable Server Status Checker</span>
                        <ToggleSwitcher value={data.enable_server_check == 1} handler={setActiveChecker}/>
                    </div>
                    <div className='row-set'>
                        <span className='setting-label'>Project Server GMT</span>
                        <select className="form-control" value={data.gmt == null ? undefined : data.gmt } onChange={setGmt}>
                            <option value='-12'>GMT -12</option>
                            <option value='-11'>GMT -11</option>
                            <option value='-10'>GMT -10</option>
                            <option value='-9'>GMT -9</option>
                            <option value='-8'>GMT -8</option>
                            <option value='-7'>GMT -7</option>
                            <option value='-6'>GMT -6</option>
                            <option value='-5'>GMT -5</option>
                            <option value='-4'>GMT -4</option>
                            <option value='-3'>GMT -3</option>
                            <option value='-2'>GMT -2</option>
                            <option value='-1'>GMT -1</option>
                            <option value='0'>GMT 0</option>
                            <option value='1'>GMT +1</option>
                            <option value='2'>GMT +2</option>
                            <option value='3'>GMT +3</option>
                            <option value='4'>GMT +4</option>
                            <option value='5'>GMT +5</option>
                            <option value='6'>GMT +6</option>
                            <option value='7'>GMT +7</option>
                            <option value='8'>GMT +8</option>
                            <option value='9'>GMT +9</option>
                            <option value='10'>GMT +10</option>
                            <option value='11'>GMT +11</option>
                            <option value='12'>GMT +12</option>
                        </select>
                    </div>
                    <div className='row-set'>
                        <span className='setting-label'>Message Filter</span>
                        <textarea className="form-control" value={messageFilter} onChange={messageFilterOnChange} onBlur={messageFilterOnBlur}/>
                    </div>
                    <div className='row-set'>
                        <span className='setting-label'>Data storage period in days</span>
                        <input className="form-control" type='text' value={archivingPeriod}
                               style={{width: '5%'}}
                               onChange={archivingPeriodOnChange}
                               onBlur={archivingPeriodOnBlur}/>
                    </div>
                </div>
            </div>
        </Fragment>);
}

const mapStateToProps = ({ project }) => {
    return { project }
}

const mapDispatchToProps = (dispatch, ownProps) => {
    const  { datastoreService } = ownProps;
    return {
        fetchProject: () => fetchProject(datastoreService, dispatch),
        fetchSetGmt: (gmt, reloadHandler) => fetchSetSetting(datastoreService, dispatch, gmt, 'gmt', reloadHandler),
        fetchSetMessageFilter: (value, reloadHandler) => fetchSetSetting(datastoreService, dispatch, value, 'message_filter', reloadHandler),
        fetchSetEnableServerCheck: (value, reloadHandler) => fetchSetSetting(datastoreService, dispatch, value, 'enable_server_check', reloadHandler),
        fetchSetArchivingPeriod: (value, reloadHandler) => fetchSetSetting(datastoreService, dispatch, value, 'archiving_period', reloadHandler)
    }
}

export default compose(
    withStoreService(),
    connect(mapStateToProps,mapDispatchToProps)
)(GeneralSettingPage)
