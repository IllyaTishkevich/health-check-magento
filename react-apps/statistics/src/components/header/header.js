import React, {useEffect, useState} from "react";
import './header.css';
import { useSearchParams } from "react-router-dom";

const Header = (props) => {
    const [ searchParams, setSearchParams ] = useSearchParams();
    const { timeFilterFrom, setTimeFilterFrom, timeFilterTo, setTimeFilterTo } = props;

    const getDiffValue = () => {
        const currentParams = Object.fromEntries([...searchParams]);
        if (currentParams.step) {
            return currentParams.step;
        } else {
            const date = currentParams['filter.date'].split('_');
            const diff = Number(date[1]) - Number(date[0]);
            if (diff <= (60 * 30)) {
                return 60;
            }
            if (diff <= (60 * 60  * 3)) {
                return(60 * 5);
            }
            if (diff <= (60 * 60  * 6)) {
                return(60 * 10);
            }
            if (diff <= (60 * 60  * 24)) {
                return(60 * 30);
            }
            if (diff <= (60 * 60 * 24 * 2)) {
                return(60 * 60);
            }
            if (diff <= (60 * 60 * 24 * 7)) {
                return(60 * 60 * 4);
            }
            if (diff <= (60 * 60 * 24 * 30)) {
                return(60 * 60 * 24);
            }

            return( 60 * 60 * 24 * 2);
        }
    };

    const oneDayHandler = () => {
        const now = new Date();
        const timestampNow = now.getTime();
        const from = now.setDate(now.getDate() - 1);
        setTimeFilterFrom(from);
        setTimeFilterTo(timestampNow);
    }

    const oneWeekHandler = () => {
        const now = new Date();
        const timestampNow = now.getTime();
        const from = now.setDate(now.getDate() - 7);
        setTimeFilterFrom(from);
        setTimeFilterTo(timestampNow);
    }

    const oneMonthHandler = () => {
        const now = new Date();
        const timestampNow = now.getTime();
        const from = now.setMonth(now.getMonth() - 1);
        setTimeFilterFrom(from);
        setTimeFilterTo(timestampNow);
    }

    const customTimeFromHandler = (e) => {
        const value = e.target.value;
        const date = new Date(value);
        setTimeFilterFrom(date.getTime());
    }

    const customTimeToHandler = (e) => {
        const value = e.target.value;
        const date = new Date(value);
        setTimeFilterTo(date.getTime());
    }

    const stepChange = (e) => {
        const value = e.target.value;
        const currentParams = Object.fromEntries([...searchParams]);
        setSearchParams({ ...currentParams, 'step': `${value}`});
    }

    const formatDate = (timestamp) => {
        if (timestamp) {
            const date = new Date(parseInt(timestamp));
            return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}T${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
        }
    }

    return  <div className='header-container'>
                <div className='title'>
                    <h1>Statistics</h1>
                </div>
                <div className='actions'>
                    <div className='btn-group'>
                        <select className="form-control" onChange={stepChange} value={getDiffValue()}>
                            <option value={60}>1 Minute</option>
                            <option value={60 * 5}>5 Minutes</option>
                            <option value={60 * 30}>30 Minutes</option>
                            <option value={60 * 60}>1 Hour</option>
                            <option value={60 * 60 * 2}>2 Hour</option>
                            <option value={60 * 60 * 4}>4 Hour</option>
                            <option value={60 * 60 * 12}>12 Hour</option>
                            <option value={60 * 60 * 24}>1 Day</option>
                            <option value={60 * 60 * 24 * 7}>7 Day</option>
                        </select>
                    </div>
                    <div className='btn-group'>
                        <button type="button" className="btn btn-default" onClick={oneDayHandler}>Day</button>
                        <button type="button" className="btn btn-default" onClick={oneWeekHandler}>Week</button>
                        <button type="button" className="btn btn-default" onClick={oneMonthHandler}>Month</button>
                    </div>
                    <div className='btn-group'>
                        <div className="input-group">
                            <span className="input-group-addon" id="basic-addon1">from</span>
                            <input className="form-control"
                                   type='datetime-local'
                                   name='from'
                                   onChange={customTimeFromHandler}
                                   value={formatDate(timeFilterFrom)}
                            />
                            <span className="input-group-addon" id="basic-addon1">to</span>
                            <input className="form-control"
                                   type='datetime-local'
                                   name='to'
                                   onChange={customTimeToHandler}
                                   value={formatDate(timeFilterTo)}
                            />
                        </div>
                    </div>
                </div>
            </div>
}

export default Header;
