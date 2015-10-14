Feature: Browser Test

  @javascript
  Scenario: I need to login to EE's control panel
    Given I am on "/admin.php"
    When I fill in "Username" with "admin"
    When I fill in "Password" with "password"
    And I press "submit"
    Then I should see "Create"
    When I follow "Create"
    When I follow "Pages"
    Given I am on "/admin.php?/cp/publish/create/1"
    Then I wait for ".grid-input-form a.btn.action"
    When I fill in "title" with "Grid Test"
    When I follow "add new row"
    When I fill in "field_id_4[rows][new_row_1][col_id_1]" with "Test 123"
    Then I press "Publish"
    Given I am on "/admin.php?/cp/publish/edit/entry/1"
    Then the "field_id_4[rows][row_id_1][col_id_1]" field should contain "Test 123"
    # Then I pause
