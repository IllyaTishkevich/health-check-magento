import React, { useMemo } from "react";
import {useSearchParams} from "react-router-dom";

import './active-filtre.css';

const ActiveFiltres = () => {
    const [ searchParams, setSearchParams ] = useSearchParams();

    const removeFilter = (filterKey) => {
        const currentParams = Object.fromEntries([...searchParams]);
        delete currentParams[filterKey];
        setSearchParams(currentParams);
    }

    const filtres = useMemo(() => {
        const currentParams = Object.fromEntries([...searchParams]);
        const filtres = Object.entries(currentParams).filter((item) => {
           const keys = item[0].split('.');
           return keys[0] == 'filter' && keys[1] !== 'date';
        });

        const buttons = filtres.map((filter) => {
            return <button type="button"
                           key={filter[0]+filter[1]}
                           className="btn btn-default filtre-button"
                           onClick={() => removeFilter(filter[0])}
            ><span className='button-label'>{filter[1]}</span><span className="glyphicon glyphicon-remove"
                                                                          aria-hidden="true"></span></button>
        });
        return buttons
    }, [searchParams]);

    if (!filtres.length) {
        return null;
    }

    return (
        <div className="panel-heading">
            { filtres }
        </div>)
}

export default ActiveFiltres;