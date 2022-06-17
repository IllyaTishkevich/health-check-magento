import React from "react";
import "./toggle-switcher.css";

const ToggleSwitcher = ({value, handler}) => {
    const onChangeHandler = (e) => {
        const value = e.target.checked;
        handler(Number(value));
    };

    return (
        <label className="switch">
            <input type="checkbox" defaultChecked={value} onChange={onChangeHandler}/>
            <span className="slider round"></span>
        </label>)
}

export default ToggleSwitcher;