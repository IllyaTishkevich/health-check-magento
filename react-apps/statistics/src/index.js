import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from "react-redux";
import { BrowserRouter as Router } from 'react-router-dom';
import App from './components/app';
import ErrorBoundry from "./components/error-boundry";
import DatastoreService from "./services/datastore-service";
import { DatastoreServiceProvider } from "./components/store-service-context";
import store from "./store";

const dataStoreService = new DatastoreService();

ReactDOM.render(
  <Provider store={store}>
      <ErrorBoundry>
          <DatastoreServiceProvider value={dataStoreService}>
              <Router>
                  <App />
              </Router>
          </DatastoreServiceProvider>
      </ErrorBoundry>
  </Provider>,
  document.getElementById('app-container')
);
