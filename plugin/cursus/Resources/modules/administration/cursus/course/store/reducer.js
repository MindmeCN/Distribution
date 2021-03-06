import {combineReducers, makeReducer} from '#/main/app/store/reducer'
import {makeListReducer} from '#/main/app/content/list/store'
import {makeFormReducer} from '#/main/app/content/form/store/reducer'
import {FORM_SUBMIT_SUCCESS} from '#/main/app/content/form/store/actions'

const reducer = combineReducers({
  list: makeListReducer('courses.list', {}, {
    invalidated: makeReducer(false, {
      [FORM_SUBMIT_SUCCESS+'/courses.current']: () => true
    })
  }),
  current: makeFormReducer('courses.current', {}, {
    users: makeListReducer('courses.current.users'),
    organizations: combineReducers({
      list: makeListReducer('courses.current.organizations.list'),
      picker: makeListReducer('courses.current.organizations.picker')
    }),
    sessions: makeListReducer('courses.current.sessions', {}, {
      invalidated: makeReducer(false, {
        [FORM_SUBMIT_SUCCESS+'/sessions.current']: () => true
      })
    })
  }),
  picker: makeListReducer('courses.picker')
})

export {
  reducer
}
