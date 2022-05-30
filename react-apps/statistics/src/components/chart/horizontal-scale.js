import React from "react";
import './chart.css';

const HorizontalScale = (props) => {
    const { board, stat } = props;

    const getTimeFormat = (date) => {
        const newDate = new Date(date);
        return `${newDate.getMonth()}-${newDate.getDate()} ${newDate.getHours()}:${newDate.getMinutes()}`;
    }

    if (board.current && stat) {
        const boundry = board.current.getBoundingClientRect();
        const widthStep = boundry.width / (stat.stat.length - 1);
        const point = stat.stat.map((point, i) => <div className='df' style={{
            left: `${(Number(widthStep) * i) - (Number(widthStep) / 2)}px`,
            width: `${Number(widthStep)}px`,
            height: `${boundry.height}px`,
        }} key={i} data-title={point.label}
        ><div className='scale-line'></div></div>);

        return <div className='horizontal-scale'>
            {point}
        </div>
    } else {
        return null;
    }
}

export default HorizontalScale;