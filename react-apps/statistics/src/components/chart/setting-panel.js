import React, {useMemo, useState} from "react";
import './setting-panel.css';

const SettingPanel = (props) => {
    const { settings, activeHandler, colorSelectHandler } = props;

    const [ isOpen, changeStatus ] = useState(false);

    const statusHandler = () => {
        changeStatus(!isOpen);
    }

    const elems = useMemo(() =>
        settings.map((level) =>
            <li key={level.key}>
                <input type='checkbox' checked={level.active} onChange={() => activeHandler(level.key)}/>
                <span style={{borderBottom: `2px solid ${level.color}`}} onClick={() => activeHandler(level.key)}>{level.key}</span>
                <select defaultValue={level.color} label-key={level.key} onChange={colorSelectHandler}>
                    <option value='red'>Red</option>
                    <option value='blue'>Blue</option>
                    <option value='green'>Green</option>
                    <option value='darkgray'>Dark Gray</option>
                </select>
            </li>
        ), [ settings ])

    const panelContent = isOpen ?
        <div className='graphic-modal-panel'>
            <ul>
                {
                    elems
                }
            </ul>
        </div>
        : null;

    return  <div className='graphic-modal'>
                <div className='graphic-modal-button'>
                    <button type='button'
                            className={ 'btn btn-default' + (isOpen ? ' open' : '')}
                            onClick={statusHandler}>
                        <span className="glyphicon glyphicon-tasks" aria-hidden="true"></span>
                    </button>
                </div>
                { panelContent }
            </div>

}

export default SettingPanel;