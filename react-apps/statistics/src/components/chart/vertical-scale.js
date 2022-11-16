import React from "react";
import './chart.css';

const VerticalScale = (props) => {
    const { board, max } = props;
    if (board.current ) {
        const boundry = board.current.getBoundingClientRect();
        const heightStep = boundry.height / (Number(max) + 1);
        const points = [];
        const step = max <= 30 ? 1 :
            max <= 100 ? 5 :
                max <= 200 ? 10 :
                    max <= 500 ? 20 :
                        max <= 1000 ? 50 :
                            max <= 2000 ? 100 : 500;
        const maxCeil = Number(step) - (max % step) + Number(max);
        for (let i = step; i <= maxCeil; i+=step) {
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