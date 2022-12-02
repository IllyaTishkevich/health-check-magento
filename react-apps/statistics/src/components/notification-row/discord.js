import React, {useState, Fragment, useMemo} from "react";

const Discord = (props) => {
    const settings = JSON.parse(props.setting);
    const { setSetting, name } = props;
    const [ url, setUrl ] = useState(settings.url || '');
    const [ error, setError ] = useState();


    const setAllSettingHandler = () => {
        setError();
        setSetting({
            url: url
        });
    }

    const onChangeInput = (e) => {
        setUrl(e.target.value);
    };

    const errorBlock = useMemo(() => {
        if (error) {
            return (
                <div className="alert alert-danger" role="alert">
                    <span className="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span className="sr-only">Error:</span>
                    { error }
                </div>)
        } else {
            return null;
        }
    }, [ error ]);

    return (
        <Fragment>
            <label htmlFor={`url-${name}`}>Webhook Url:</label>
            <input type='text'
                   id={`url-${name}`}
                   size='114'
                   onChange={onChangeInput}
                   value={url} onBlur={setAllSettingHandler}
                   placeholder='https://discord.com/api/webhooks/...'/>
            {errorBlock}
        </Fragment>);
}

export default Discord;