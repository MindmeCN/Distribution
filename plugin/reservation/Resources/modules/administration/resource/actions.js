import {url} from '#/main/app/api'
import {makeActionCreator} from '#/main/app/store/actions'
import {API_REQUEST} from '#/main/app/api'
import {actions as formActions} from '#/main/app/content/form/store'
import {actions as listActions} from '#/main/app/content/list/store'

const RESOURCE_RIGHTS_ADD = 'RESOURCE_RIGHTS_ADD'
const RESOURCE_RIGHTS_UPDATE = 'RESOURCE_RIGHTS_UPDATE'

const actions = {}

actions.addResourceRights = makeActionCreator(RESOURCE_RIGHTS_ADD, 'resourceRights')
actions.updateResourceRights = makeActionCreator(RESOURCE_RIGHTS_UPDATE, 'id', 'value')

actions.openForm = (formName, id = null) => (dispatch) => {
  if (id) {
    dispatch({
      [API_REQUEST]: {
        url: ['apiv2_reservationresource_get', {id}],
        success: (response, dispatch) => {
          dispatch(formActions.resetForm(formName, response, false))
          dispatch(listActions.invalidateData('resourceForm.organizations'))
        }
      }
    })
  } else {
    dispatch(formActions.resetForm(formName, {name: null, quantity: 1, maxTimeReservation: '00:00:00'}, true))
    dispatch(listActions.invalidateData('resourceForm.organizations'))
  }
}

actions.addOrganizations = (id, organizations) => ({
  [API_REQUEST]: {
    url: url(['apiv2_reservationresource_add_organizations', {id: id}], {ids: organizations}),
    request: {
      method: 'PATCH'
    },
    success: (data, dispatch) => {
      dispatch(listActions.invalidateData('resources'))
      dispatch(listActions.invalidateData('resourceForm.organizations'))
    }
  }
})

actions.addRoles = (id, resourceRights, roles) => (dispatch) => {
  roles.forEach(roleId => {
    const rights = resourceRights.find(rr => rr.role.id === roleId)

    if (!rights) {
      dispatch({
        [API_REQUEST]: {
          url: ['apiv2_reservationresourcerights_create'],
          request: {
            method: 'POST',
            body: JSON.stringify({
              resource: {
                id: id
              },
              role: {
                id: roleId
              }
            })
          },
          success: (response, dispatch) => {
            dispatch(actions.addResourceRights(response))
            dispatch(listActions.invalidateData('resources'))
            dispatch(listActions.invalidateData('resourceForm'))
          }
        }
      })
    }
  })
}

actions.editResourceRights = (rights, value) => ({
  [API_REQUEST]: {
    url: ['apiv2_reservationresourcerights_update', {id: rights.id}],
    request: {
      method: 'PUT',
      body: JSON.stringify(Object.assign({}, rights, {mask: value}))
    },
    success: (data, dispatch) => {
      dispatch(actions.updateResourceRights(rights.id, value))
    }
  }
})

actions.exportResources = (resources) => () => {
  window.location.href = url(['apiv2_reservationresource_export'], {ids: resources.map(r => r.id)})
}

export {
  actions,
  RESOURCE_RIGHTS_ADD,
  RESOURCE_RIGHTS_UPDATE
}