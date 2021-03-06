import {createSelector} from 'reselect'

const STORE_NAME = 'resource'

const resource = (state) => state[STORE_NAME]

const text = createSelector(
  [resource],
  (resource) => resource.text
)

export const selectors = {
  STORE_NAME,
  resource,
  text
}
