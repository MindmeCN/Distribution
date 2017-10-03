/* global process, require */

/**
 * This file was copied and update from QuestionBank bundle
 */


import {
  applyMiddleware,
  compose,
  createStore as baseCreate
} from 'redux'
import thunk from 'redux-thunk'
import {apiMiddleware} from './api/middleware'
import {lexiconApp} from './reducers/index'

const middleware = [apiMiddleware, thunk]

if (process.env.NODE_ENV !== 'production') {
  const freeze = require('redux-freeze')
  middleware.push(freeze)
}

export function createStore(initialState) {
  return baseCreate(
    lexiconApp,
    initialState,
    compose(
      applyMiddleware(...middleware),
      window.devToolsExtension ? window.devToolsExtension() : f => f
    )
  )
}
