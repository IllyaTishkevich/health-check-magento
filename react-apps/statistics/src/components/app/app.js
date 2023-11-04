import React from "react";
import StatisticPage from '../../pages/statistic-page';
import ItemPage from "../../pages/item-page";
import SettingPage from "../../pages/setting-page";
import { Routes, Route } from "react-router-dom";

const  App = () => {
    return (
        <Routes>
            <Route path='/stat/index' element={<StatisticPage />} />
            <Route path='/stat/item' element={<ItemPage />} exact/>
            <Route path='/project/view' element={<SettingPage />} />
            <Route path='/healthcheck/web/stat/index' element={<StatisticPage />} />
            <Route path='/healthcheck/web/stat/item' element={<ItemPage />}/>
            <Route path='/healthcheck/web/project/view' element={<SettingPage />} />
            <Route path='/' element={<SettingPage />} exact/>
        </Routes>)
}

export default App;