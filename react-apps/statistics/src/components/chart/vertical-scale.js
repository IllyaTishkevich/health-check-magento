import React from "react";
import './chart.css';

const VerticalScale = (props) => {
    const { board, max } = props;
    if (board.current ) {
        const boundry = board.current.getBoundingClientRect();
        const heightStep = boundry.height / (max + 1);
        const points = [];
        for (let i = 1; i <= max; i++) {
            points.push(<div key={`${i}sc`}style={{top: `${boundry.height - (i * heightStep) - 10 }px`}}
                            className='vertical-scale-point'>{i}
                            <div className='vertical-scale-point-line' style={{width:`${boundry.width + 10}px`}}></div>
            </div>);
        }
        return <div className='vertical-scale'>
            { points }
        </div>
    } else {
        return null;
    }
}

export default VerticalScale