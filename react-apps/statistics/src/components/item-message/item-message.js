import React from "react";

import './item-message.css';

const ItemMessage = ({ data, level }) => {
    const jsonObject = JSON.parse(data.message);

    const parser = (row) => {
        if (Array.isArray(row)) {
            switch (typeof row[1]) {
                case 'string':
                    return <div className='pars-row'><span>"{row[0]}"</span>:<span>"{row[1]}"</span></div>
                case 'number':
                    return <div className='pars-row'><span>"{row[0]}"</span>:<span>"{row[1]}"</span></div>
                case 'object':
                    if (row[1] === null) {
                        return <div className='pars-row'><span>"{row[0]}"</span>:<span>---</span></div>
                    }
                    const items = Array.isArray(row[1]) ? row[1].map(parser) : Object.entries(row[1]).map(parser);
                    return (
                        <div className='pars-row'>
                            <span>"{row[0]}"</span>:<span className="glyphicon glyphicon-triangle-bottom" aria-hidden="true"></span>
                            <div className='pars-row'>{items}</div>
                        </div>);
                default :
                    return null;
            }
        } else {
            return <div className='pars-row'>[{Object.entries(row).map(parser)}]</div>
        }
    }

    const html = Object.entries(jsonObject).map(parser);

    return (
        <div className="panel panel-default">
            <div className="panel-heading">
                <div className='header-title-block'>
                    <span className='level-code'>{ level.key }</span>
                    <span className='message-creaeted'>{ data.create }</span>
                </div>
            </div>
            <div className="panel-body">
                <div>{html}</div>
            </div>
        </div>)
}

export default ItemMessage;

