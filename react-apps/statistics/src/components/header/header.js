import React, {useEffect, useState} from "react";
import './header.css';
import { useSearchParams } from "react-router-dom";
import { setGmt, getGmt } from "../../setting-action";

const Header = (props) => {
    const [ searchParams, setSearchParams ] = useSearchParams();
    const [ gmt, setGmtState ] = useState();
    const { timeFilterFrom, setTimeFilterFrom, timeFilterTo, setTimeFilterTo } = props;

    useEffect(() => {
        const sysGmt = getGmt();
        if (sysGmt) {
            setGmtState(sysGmt);
        } else {
            const date = new Date();
            const currentGmt = date.getTimezoneOffset() / 60;
            setGmtState(currentGmt);
            setGmt(currentGmt);
        }
    }, []);

    useEffect(() => {

    }, [ gmt ]);

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

    const gmtChange = (e) => {
        const value = e.target.value;
        setGmt(value);
        setGmtState(value);
    }

    const formatDate = (timestamp) => {
        if (timestamp) {
            const currentGmt = getGmt();
            const hint = Number(currentGmt) * 60 * 60 * 1000;
            const date = new Date(parseInt(timestamp));
            // const str =  date.toISOString();
            // return str.substring(0, str.length - 8);
            return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}T${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
        }
    }

    return  <div className='header-container'>
                <div className='title'>
                    <h1>Statistics</h1>
                </div>
                <div className='actions'>
                    <div className='btn-group'>
                        <select className="form-control" onChange={gmtChange} value={gmt}>
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
