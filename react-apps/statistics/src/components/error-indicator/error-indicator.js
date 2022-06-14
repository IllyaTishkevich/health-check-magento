import React from "react";

const  ErrorIndicator = ({message}) => {
    return (
    <div className="alert alert-danger" role="alert">
        <span className="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span className="sr-only">Error:</span>
        { message }
    </div>)
}

export default ErrorIndicator;