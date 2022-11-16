import React from "react";
import { useSearchParams } from "react-router-dom";

import './pagination.css';

const Pagination = ({ pagination }) => {
    const [ searchParams, setSearchParams ] = useSearchParams();
    const setPage = (number) => {
        const currentParams = Object.fromEntries([...searchParams]);
        setSearchParams({ ...currentParams, 'page': `${number}`});
    };

    let list = [];
    const first = pagination.page < 4 ? 1 :  pagination.page - 2;

    if (first > 1) {
        list.push(<li className={1 == pagination.page ? "active" : ''} key={1}>
            <a onClick={() => setPage(1)}>{1}</a>
        </li>)
        if(first > 2) {
            list.push(<li key={'first-dot'}>
                <a>...</a>
            </li>)
        }
    }

    for (let i = first; i < first + 5; i++) {
        if (i <= pagination.pageCount) {
            list.push(<li className={i == pagination.page ? "active" : ''} key={i}>
                <a onClick={() => setPage(i)}>{i}</a>
            </li>)
        }
    }

    if (first + 5 < pagination.pageCount) {
        if(first + 2 < pagination.pageCount) {
            list.push(<li key={'first-dot'}>
                <a>...</a>
            </li>)
        }
        list.push(<li className={pagination.pageCount == pagination.page ? "active" : ''} key={pagination.pageCount}>
            <a onClick={() => setPage(pagination.pageCount)}>{pagination.pageCount}</a>
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