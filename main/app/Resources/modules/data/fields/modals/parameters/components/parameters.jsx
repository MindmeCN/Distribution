import React, {Component} from 'react'
import {PropTypes as T} from 'prop-types'
import merge from 'lodash/merge'
import set from 'lodash/set'

import {trans} from '#/main/core/translation'
import {getType} from '#/main/app/data'
import {FormDataModal} from '#/main/app/modals/form/components/data'

class ParametersModal extends Component {
  constructor(props) {
    super(props)

    this.state = {
      options: props.data.options ? props.data.options : {},
      restrictions: props.data.restrictions ? props.data.restrictions : {},
      typeDef: null
    }

    this.updateOptions = this.updateOptions.bind(this)
    this.updateRestrictions = this.updateRestrictions.bind(this)
  }

  componentDidMount() {
    getType(this.props.data.type).then(definition => this.setState({typeDef: definition}))
  }

  /**
   * We locally manage a copy of current options to be able
   * to configure options form based on current values.
   *
   * This is only used to be passed to `typeDefinition.configure()`
   * which generate the form for the current data type field.
   *
   * @param {string} optionName
   * @param {*}  optionValue
   */
  updateOptions(optionName, optionValue) {
    const newOptions = merge({}, this.state.options)

    set(newOptions, optionName, optionValue)

    this.setState({
      options: newOptions
    })
  }

  updateRestrictions(restrictionName, restrictionValue) {
    const newRestrictions = merge({}, this.state.restrictions)

    set(newRestrictions, restrictionName, restrictionValue)

    this.setState({
      restrictions: newRestrictions
    })
  }

  render() {
    return (
      <FormDataModal
        {...this.props}
        save={fieldData => {
          // generate normalized name for field (c/p from api Entity)
          let normalizedName = fieldData.label.replace(new RegExp(' ', 'g'), '-') // Replaces all spaces with hyphens.
          normalizedName = normalizedName.replace(/[^A-Za-z0-9-]/g, '') // Removes special chars.
          normalizedName = normalizedName.replace(/-+/g, '-') // Replaces multiple hyphens with single one.
          const required = fieldData.restrictions.locked && !fieldData.restrictions.lockedEditionOnly ?
            false :
            fieldData.required
          const restrictions = merge({}, fieldData.restrictions)

          if (!fieldData.restrictions.locked) {
            restrictions['lockedEditionOnly'] = false
          }

          this.props.save(merge({}, fieldData, {
            name: normalizedName,
            required: required,
            restrictions: restrictions
          }))
        }}
        title={trans('edit_field')}
        sections={[
          {
            id: 'general',
            title: trans('general'),
            primary: true,
            fields: [
              {
                name: 'type',
                type: 'string',
                label: trans('type'),
                readOnly: true,
                hideLabel: true,
                calculated: () => this.state.typeDef ? this.state.typeDef.meta.label : ''
              }, {
                name: 'label',
                type: 'string',
                label: trans('name'),
                required: true
              }, {
                name: 'required',
                type: 'boolean',
                label: trans('field_optional'),
                displayed: !this.state.restrictions.locked || this.state.restrictions.lockedEditionOnly,
                options: {
                  labelChecked: trans('field_required')
                }
              }
            ]
          }, this.state.typeDef && {
            id: 'parameters',
            icon: 'fa fa-fw fa-cog',
            title: trans('parameters'),
            fields: this.state.typeDef.configure(this.state.options).map(optionField => merge({}, optionField, {
              name: `options.${optionField.name}`, // store all options in an `options` sub object
              onChange: (value) => this.updateOptions(optionField.name, value)
            }))
          }, {
            id: 'help',
            icon: 'fa fa-fw fa-info',
            title: trans('help'),
            fields: [
              {
                name: 'help',
                type: 'string',
                label: trans('message'),
                options: {
                  long: true
                }
              }
            ]
          }, {
            id: 'restrictions',
            icon: 'fa fa-fw fa-key',
            title: trans('access_restrictions'),
            fields: [
              {
                name: 'restrictions.isMetadata',
                type: 'boolean',
                label: trans('confidential_data'),
                onChange: (value) => this.updateRestrictions('isMetadata', value)
              }, {
                name: 'restrictions.hidden',
                type: 'boolean',
                label: trans('hide_field'),
                onChange: (value) => this.updateRestrictions('hidden', value)
              }, {
                name: 'restrictions.locked',
                type: 'boolean',
                label: trans('locked'),
                options: {
                  help: trans('required_locked_conflict')
                },
                onChange: (value) => this.updateRestrictions('locked', value),
                linked: [
                  {
                    name: 'restrictions.lockedEditionOnly',
                    type: 'boolean',
                    label: trans('edition_only'),
                    displayed: this.state.restrictions.locked,
                    onChange: (value) => this.updateRestrictions('lockedEditionOnly', value)
                  }
                ]
              }, {
                name: 'restrictions.order',
                type: 'number',
                label: trans('order'),
                onChange: (value) => this.updateRestrictions('order', value)
              }
            ]
          }
        ].filter(section => !!section)}
      />
    )
  }
}

ParametersModal.propTypes = {
  data: T.shape({
    type: T.string.isRequired,
    options: T.object,
    restrictions: T.object
  }),
  fadeModal: T.func.isRequired,
  save: T.func.isRequired
}

export {
  ParametersModal
}
