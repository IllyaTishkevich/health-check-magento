import React from "react";
import "./toggle-switcher.css";

const ToggleSwitcher = ({value, handler, id}) => {
    const onChangeHandler = (e) => {
        const value = e.target.checked;
        handler(Number(value));
    };

    return (
        <label className="switch">
            <input id={id} type="checkbox" defaultChecked={value} onChange={onChangeHandler}/>
            <span className="slider round"></span>
        </label>)
}

export default ToggleSwitcher;