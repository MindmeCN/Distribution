import React from 'react'
import ReactDOM from 'react-dom'
import {Provider} from 'react-redux'
import {createStore} from '#/main/app/store'
import {registerModals} from '#/main/core/layout/modal'
import {reducers} from './reducers'
import {EventFormModal} from './components/event-form-modal.jsx'
import {EventRepeatFormModal} from './components/event-repeat-form-modal.jsx'
import {SessionEventsToolLayout} from './components/session-events-tool-layout.jsx'
import {EventCommentsModal} from './components/event-comments-modal.jsx'
import {EventSetFormModal} from './components/event-set-form-modal.jsx'
import {EventSetRegistrationModal} from './components/event-set-registration-modal.jsx'

// TODO : rewrite using last core components

class SessionEventsTool {
  constructor(workspaceId, canEdit, sessions, events, eventsUsers) {
    registerModals([
      ['MODAL_EVENT_FORM', EventFormModal],
      ['MODAL_EVENT_REPEAT_FORM', EventRepeatFormModal],
      ['MODAL_EVENT_COMMENTS', EventCommentsModal],
      ['MODAL_EVENT_SET_FORM', EventSetFormModal],
      ['MODAL_EVENT_SET_REGISTRATION', EventSetRegistrationModal]
    ])
    const sessionId = sessions.length === 1 ? sessions[0]['id'] : null
    this.store = createStore(
      reducers,
      {
        workspaceId: workspaceId,
        canEdit: canEdit,
        disableRegistration: disableRegistration,
        sessions: sessions,
        sessionId: sessionId,
        events: {
          data: events,
          totalResults: eventsTotal
        },
        eventsUsers: eventsUsers
      }
    )
  }

  render(element) {
    ReactDOM.render(
      React.createElement(
        Provider,
        {store: this.store},
        React.createElement(SessionEventsToolLayout)
      ),
      element
    )
  }
}

const container = document.querySelector('.session-events-tool-container')
const workspaceId = parseInt(container.dataset.workspace)
const canEdit = parseInt(container.dataset.editable)
const disableRegistration = parseInt(container.dataset.disableRegistration)
const sessions = JSON.parse(container.dataset.sessions)
const events = JSON.parse(container.dataset.events)
const eventsTotal = parseInt(container.dataset.eventsTotal)
const eventsUsers = JSON.parse(container.dataset.eventsUsers)
const tool = new SessionEventsTool(workspaceId, canEdit, sessions, events, eventsUsers)

tool.render(container)