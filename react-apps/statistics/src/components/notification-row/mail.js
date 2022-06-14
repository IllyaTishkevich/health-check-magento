import React, {useState, Fragment, useMemo} from "react";

const Mail = (props) => {
    const settings = JSON.parse(props.setting);
    const { setSetting, name } = props;
    const [ mail, setMail ] = useState(settings.mail ? settings.mail : '');
    const [ error, setError ] = useState();

    const validateEmail = (email) => {
        const re = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
        return re.test(String(email).toLowerCase());
    };

    const setAllSettingHandler = () => {
        if (validateEmail(mail)) {
            setError();
            setSetting({
                mail: mail
            });
        } else {
            setError('is not valid email');
        }
    };

    const onChangeInput = (e) => {
        setMail(e.target.value);
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
            <label htmlFor={`mail-${name}`}>Send mail to:</label>
            <input type='email'
                   id={`mail-${name}`}
                   onChange={onChangeInput}
                   value={mail} onBlur={setAllSettingHandler}
                   placeholder='email'/>
            {errorBlock}
        </Fragment>);
}

export default Mail;