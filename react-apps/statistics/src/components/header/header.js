import React, { useState, useEffect } from "react";
import './header.css';
import { useSearchParams } from "react-router-dom";

const Header = () => {
    const [ searchParams, setSearchParams ] = useSearchParams();
    const [ timeFilterFrom, setTimeFilterFrom ] = useState();
    const [ timeFilterTo, setTimeFilterTo ] = useState();

    useEffect(() => {
        if (timeFilterFrom && timeFilterTo) {
            const currentParams = Object.fromEntries([...searchParams]);
            setSearchParams({ ...currentParams, 'filter.date': `${timeFilterFrom}_${timeFilterTo}`});
        }
    }, [searchParams, timeFilterFrom, timeFilterTo]);

    const oneDayHandler = () => {
        const now = new Date();
        const timestampNow = now.getTime();
        const from = now.setDate(now.getDate() - 1);
        const currentParams = Object.fromEntries([...searchParams]);
        setSearchParams({ ...currentParams, 'filter.date': `${from}_${timestampNow}`});
    }

    const oneWeekHandler = () => {
        const now = new Date();
        const timestampNow = now.getTime();
        const from = now.setDate(now.getDate() - 7);
        const currentParams = Object.fromEntries([...searchParams]);
        setSearchParams({ ...currentParams, 'filter.date': `${from}_${timestampNow}`});
    }

    const oneMonthHandler = () => {
        const now = new Date();
        const timestampNow = now.getTime();
        const from = now.setMonth(now.getMonth() - 1);
        const currentParams = Object.fromEntries([...searchParams]);
        setSearchParams({ ...currentParams, 'filter.date': `${from}_${timestampNow}`});
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

    return  <div className='header-container'>
                <div className='title'>
                    <h1>Statistics</h1>
                </div>
                <div className='actions'>
                    <div className='btn-group'>
                        <select className="form-control" onChange={stepChange}>
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
                            <input className="form-control" type='datetime-local' name='from' onChange={customTimeFromHandler}/>
                            <span className="input-group-addon" id="basic-addon1">to</span>
                            <input className="form-control" type='datetime-local' name='to' onChange={customTimeToHandler}/>
                        </div>
                    </div>
                </div>
            </div>
}

export default Header;
