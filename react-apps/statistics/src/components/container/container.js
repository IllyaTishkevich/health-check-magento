import React, { useEffect } from "react";
import Spinner from "../spiner";
import ErrorIndicator from "../error-indicator";
import { connect} from "react-redux";
import { withStoreService } from '../hoc';
import { fetchMessages, fetchLevels } from "../../actions";
import { compose } from '../../utils';


const Container = (props) => {
    const { messages,levels, fetchMessages, fetchLevels } = props;

    useEffect(() => {
        if( messages.data.length === 0 ) {
            fetchMessages();
        }

        if( levels.data.length === 0 ) {
            fetchLevels();
        }
    }, [messages.data.length, levels.data.length]);

    if (messages.loading) {
        return (
            <Spinner />
        );
    }

    if (messages.error !== null) {
        return <ErrorIndicator message={messages.error}/>
    }

    return <div>

    </div>

}

const mapStateToProps = (props) => {
    const { messages, levels } = props;
    return { messages, levels }
}

const mapDispatchToProps = (dispatch, ownProps) => {
    const  { datastoreService } = ownProps;
    return {
        fetchMessages: () => fetchMessages(datastoreService, dispatch),
        fetchLevels: () => fetchLevels(datastoreService, dispatch),
    }
}

export default compose(
    withStoreService(),
    connect(mapStateToProps,mapDispatchToProps)
)(Container)