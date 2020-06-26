/**
* PLEASE DO NOT MODIFY THIS FILE. WORK ON THE ES6 VERSION.
* OTHERWISE YOUR CHANGES WILL BE REPLACED ON THE NEXT BUILD.
**/

/**
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
((document, submitForm) => {
  'use strict'; // Selectors used by this script

  const buttonDataSelector = 'data-submit-task';
  const formId = 'adminForm';
  /**
   * Submit the task
   * @param task
   */

  const submitTask = task => {
    const form = document.getElementById(formId);

    if (form && task === 'subscriber.batch') {
      submitForm(task, form);
    }
  }; // Register events


  document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('batch-submit-button-id');

    if (button) {
      button.addEventListener('click', e => {
        const task = e.target.getAttribute(buttonDataSelector);
        submitTask(task);
      });
    }
  });
})(document, Joomla.submitform);
