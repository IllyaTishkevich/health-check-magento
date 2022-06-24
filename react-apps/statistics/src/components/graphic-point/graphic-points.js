import React, { useMemo } from "react";
import './graphic-lines.css';
import { useSearchParams } from "react-router-dom";

const GraphicPoints = (props) => {
    const { setTimeFilterFrom, setTimeFilterTo } = props;
    const { board, stat, max, levelSetting, level: { key } } = props;
    const [ searchParams, setSearchParams ] = useSearchParams();

    const setPointHandler = (label, key) => {
        const currentParams = Object.fromEntries([...searchParams]);
        setSearchParams({ ...currentParams, 'filter.level': `${key.toLowerCase()}`});
        const date = label.split(' - ');
        const from = new Date(date[0]);
        const to = new Date(date[1]);
        setTimeFilterFrom(from.getTime());
        setTimeFilterTo(to.getTime());
        // setSearchParams({ ...currentParams, 'filter.date': `${from.getTime()}_${to.getTime()}`});
    };

    const thisSettings = useMemo(() => {
        for(let i = 0; i < levelSetting.length; i++) {
            if (levelSetting[i].key == key) {
                return levelSetting[i];
            }
        }
    }, [levelSetting]);

    if(!thisSettings.active) {
        return null;
    }
    if (!board.current) {
        return null;
    }

    const hex2rgb = (c) => {
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(c);
        return result ? `rgb(${parseInt(result[1], 16)}, ${parseInt(result[2], 16)}, ${parseInt(result[3], 16)})`
            : null;
    }

    const boundry = board.current.getBoundingClientRect();
    const widthStep = boundry.width / (stat.stat.length - 1);
    const heightStep = boundry.height / (Number(max) + 1);
    const points = stat.stat.map((point, i) => (
        <div className='point' style={{
                            left: `${(Number(widthStep) * i)  - 5}px`,
                            top: `${boundry.height - 5 - (heightStep * point.count)}px`,
                            backgroundColor: thisSettings.color}}
             key={i}
             data-title={`${key}:${point.count}`}
             onClick={() => setPointHandler(point.label, key)}
            ></div>))

    let lines = `m0,${boundry.height}`;

    for (let i = 1; i < stat.stat.length; i++ ) {
        lines += ` L${Number(widthStep) * i},${boundry.height - (heightStep * stat.stat[i].count)}`
    }

    return <div className='graphic-line'
        style={{background: `url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg"><path d="${lines}" stroke-width="5px" fill="none" stroke="${hex2rgb(thisSettings.color)}"/></svg>')`,
                height: `${boundry.height}px`}}>
        { points }
    </div>
}

export default GraphicPoints;