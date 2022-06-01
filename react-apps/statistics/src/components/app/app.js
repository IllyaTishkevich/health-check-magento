import React from "react";
import StatisticPage from '../../pages/statistic-page';
import ItemPage from "../../pages/item-page";
import { Routes, Route } from "react-router-dom";

const  App = () => {
    return (
        <Routes>
            <Route path='/stat/index' element={<StatisticPage />} />
            <Route path='/stat/item' element={<ItemPage />} exact/>
            <Route path='healthcheck/web/stat/index' element={<StatisticPage />} />
            <Route path='healthcheck/web/stat/item' element={<ItemPage />} exact/>
            <Route path='/' element={<StatisticPage />} exact/>
        </Routes>)
}

export default App;