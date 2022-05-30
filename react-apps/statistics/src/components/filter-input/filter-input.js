import React, { useState } from "react";
import { useSearchParams } from "react-router-dom";
import './filter-input.css';

const FilterInput = (props) => {
    const { code } = props;
    const [ searchParams, setSearchParams ] = useSearchParams();

    const currentParams = Object.fromEntries([...searchParams]);
    const [ value, setValue ] = useState(currentParams[`filter.${code}`]);

    const filterHandler = (e) => {
            setValue(e.target.value);
    };

    const onSubmitFilter = (e) => {
        if (e.key === 'Enter') {
            const currentParams = Object.fromEntries([...searchParams]);
            if (value !== '') {
                currentParams[`filter.${code}`] = `${value.toLowerCase()}`;
            } else {
                delete currentParams[`filter.${code}`];
            }
            setSearchParams({...currentParams});
        }
    };

    return <input type="text"
                  className="form-control filter-input"
                  onChange={filterHandler}
                  onKeyUp={onSubmitFilter}
                  value={value}/>
}

export default FilterInput