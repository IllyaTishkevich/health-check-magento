import React from "react";
import { useSearchParams } from "react-router-dom";

import './pagination.css';

const Pagination = (props) => {
    const { pagination } = props;
    const [ searchParams, setSearchParams ] = useSearchParams();
    const setPage = (number) => {
        const currentParams = Object.fromEntries([...searchParams]);
        setSearchParams({ ...currentParams, 'page': `${number}`});
    };

    let list = [];
    for (let i = 1; i<= pagination.pageCount; i++) {
        list.push(<li className={i == pagination.page ? "active" : ''} key={i}>
            <a onClick={() => setPage(i)}>{i}</a>
        </li>)
    }
    return (
        <nav aria-label="Page navigation">
            <ul className="pagination">
                { pagination.prevPage ? (
                        <li>
                            <a  aria-label="Previous" onClick={() => setPage(pagination.prevPage)}>
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    ) : null
                }
                { list }
                { pagination.nextPage ? (
                        <li>
                            <a aria-label="Next" onClick={() => setPage(pagination.nextPage)}>
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    ) : null
                }
            </ul>
        </nav>)
}

export default Pagination;