import React, { useEffect, useMemo, useState, useRef } from "react";
import SettingPanel from "./setting-panel";
import './chart.css';

import { fetchLevelStat, fetchMessageStat } from "../../actions";
import { compose } from "../../utils";
import { withStoreService } from "../hoc";
import { connect } from "react-redux";

import { useSearchParams } from "react-router-dom";
import GraphicPoints from "../graphic-point";
import HorizontalScale from "./horizontal-scale";

import { setLevelColor, getLevelColor, getLevelActivity, setLevelActivity } from "../../setting-action";
import VerticalScale from "./vertical-scale";
import Header from "../header";

const getRandomColor = () => {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

const Chart = (props) => {
    const [ timeFilterFrom, setTimeFilterFrom ] = useState();
    const [ timeFilterTo, setTimeFilterTo ] = useState();

    const board = useRef();
    const { levels, stat, fetchLevelStat, fetchMessageStat, messageId } = props;
    const [ searchParams, setSearchParams ] = useSearchParams();
    const defaultSetting = useMemo(() => levels.map((level) => {
        const color = getLevelColor(level.key) ? getLevelColor(level.key) : getRandomColor();

        if (!getLevelColor(level.key)) {
            setLevelColor(level.key, color);
        }
        const item =  {
            id: level.id,
            key: level.key,
            active: getLevelActivity(level.key) !== undefined ? getLevelActivity(level.key) : true,
            color: color
        };
        return item;
    }),[ levels]);

    const [ levelSetting, setLevelSetting ] = useState(defaultSetting);

    const activeHandler = (key) => {
        const newSetting = levelSetting.map((level) => {
            if (key == level.key) {
                setLevelActivity(key, !level.active);
            }
            const item =  {
                id: level.id,
                key: level.key,
                active: key == level.key ? !level.active : level.active,
                color: level.color
            };
            return item;
        })

        setLevelSetting(newSetting);
    }
    const colorSelectHandler = (e) => {
        const color = e.target.value;
        const key = e.target.getAttribute('label-key');
        const newSetting = levelSetting.map((level) => {
            const item =  {
                id: level.id,
                key: level.key,
                active: level.active,
                color: key == level.key ? color : level.color
            };
            return item;
        })
        setLevelColor(key, color);
        setLevelSetting(newSetting);
    }

    const isLevelActive = (key) => {
        for (const level in levelSetting) {
            if (levelSetting[level].key == key.toUpperCase()) {
                return levelSetting[level].active
            }
        }
        return false;
    }

    useEffect(() => {
        if (levels && messageId == undefined) {
            for (const i in levels) {
                if (isLevelActive(levels[i].key)) {
                    fetchLevelStat(levels[i].key);
                }
            }
        }
        if (messageId) {
            fetchMessageStat(messageId);
        }
    }, [stat.data, levels, searchParams, messageId, levelSetting]);

    useEffect(() => {
        if (timeFilterFrom && timeFilterTo) {
            const currentParams = Object.fromEntries([...searchParams]);
            setSearchParams({ ...currentParams, 'filter.date': `${timeFilterFrom}_${timeFilterTo}`});
        } else {
            const currentParams = Object.fromEntries([...searchParams]);
            if (currentParams['filter.date']) {
                const date = currentParams['filter.date'].split('_');
                setTimeFilterFrom(date[0]);
                setTimeFilterTo(date[1]);
            }
        }
    }, [searchParams, timeFilterFrom, timeFilterTo]);

    const [ lines, horizontalScale, verticalScale ] = useMemo(() => {
        let max = 0;
        for (const k in stat.data) {
            if (isLevelActive(k)) {
                max = Number(stat.data[k].sets.max) > Number(max) ? Number(stat.data[k].sets.max) : Number(max);
            }
        }

        const horScale = <HorizontalScale stat={stat.data[levels[0].key.toLowerCase()]} board={board}/>

        const verScale = <VerticalScale board={board} max={max} />

        const lines = levels.map((level) => {
            const key = level.key.toLowerCase();
            return stat.data[key] ? <GraphicPoints board={board}
                                                   stat={stat.data[key]}
                                                   key={level.key}
                                                   level={level}
                                                   max={max}
                                                   levelSetting={levelSetting}
                                                   setTimeFilterFrom={setTimeFilterFrom}
                                                   setTimeFilterTo={setTimeFilterTo}
            /> : null;
        })

        return [ lines, horScale, verScale ]
    }, [stat, levelSetting, board])

    return (
        <div className="panel panel-default">
            <div className="panel-body">
                <Header timeFilterFrom={timeFilterFrom}
                        setTimeFilterFrom={setTimeFilterFrom}
                        timeFilterTo={timeFilterTo}
                        setTimeFilterTo={setTimeFilterTo}/>
            </div>
            <div className="panel-footer">
                <div className="chart-container">
                            <SettingPanel settings={levelSetting}
                                          activeHandler={activeHandler}
                                          colorSelectHandler={colorSelectHandler}/>
                            <div className='graphic'>
                                <div className='background' ref={board}>
                                    { verticalScale }
                                    { horizontalScale }
                                    { lines }
                                </div>
                            </div>
                       </div>
            </div>
        </div>)
}

const mapStateToProps = ({ stat }) => {
    return { stat }
}

const mapDispatchToProps = (dispatch, ownProps) => {
    const  { datastoreService } = ownProps;
    return {
        fetchLevelStat: (level) => fetchLevelStat(datastoreService, dispatch, level),
        fetchMessageStat: (id) => fetchMessageStat(datastoreService, dispatch, id)
    }
}

export default compose(
    withStoreService(),
    connect(mapStateToProps,mapDispatchToProps)
)(Chart)