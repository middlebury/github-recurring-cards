<?php

namespace GithubRecurringCards;

/**
 * A definition of a recurring card.
 */
class Card {

  protected static $optional_elements = array(
    'title',
    'body',
    'labels',
    'subtasks',
  );
  protected static $board_custom_fields = array();

  public function __construct(array $data) {
    // hour
    if (empty($data['hour'])
      || filter_var($data['hour'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 0, 'max_range' => 23))) === false)
    {
      throw new \Exception("hour must be an integer between 0 and 23. ".$data['hour']." given.");
    }
    // start_date
    if (empty($data['start_date']) || !preg_match('/^\d\d\d\d-\d\d-\d\d$/', $data['start_date'])) {
      throw new \Exception("start_date must be a valid date string in the YYYY-MM-DD format. ".$data['start_date']." given.");
    }
    // board
    if (empty($data['board'])
      || filter_var($data['board'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 0))) === false)
    {
      throw new \Exception("board must be an integer between 0 and 23. ".$data['board']." given.");
    }
    // recurrence
    if (empty($data['recurrence']) || !preg_match('/FREQ=.+/i', $data['recurrence'])) {
      throw new \Exception("recurrence an Recurrence-Rule supported by https://github.com/simshaun/recurr as defined in https://tools.ietf.org/html/rfc2445#section-4.3.10 , for example: 'FREQ=WEEKLY;BYDAY=MO'. '".$data['recurrence']."' given.");
    }
    $this->data = $data;
  }

  public function recurrsBetween(\DateTime $after, \DateTime $before) {
    $my_start = new \DateTime($this->data['start_date'].' '.$this->data['hour'].':00:00');
    $my_end = new \DateTime($this->data['start_date'].' '.$this->data['hour'].':30:00');
    try {
      $recurrRule = new \Recurr\Rule($this->data['recurrence'], $my_start, $my_end);
      $between = new \Recurr\Transformer\Constraint\BetweenConstraint($after, $before, true);
      $transformer = new \Recurr\Transformer\ArrayTransformer();
      $recurrences = $transformer->transform($recurrRule, $between);
    } catch (\Exception $e) {
      throw new \Exception("Error creating recurrence rule from '".$this->data['recurrence']."': ".$e->getMessage());
    }
    return (count($recurrences) > 0);
  }

  public function addToGithub(\Github\Client $github) {
    $data = array();
    foreach (self::$optional_elements as $key) {
      if (!empty($this->data[$key])) {
        $data[$key] = $this->data[$key];
      }
    }
    if (!empty($data['subtasks'])) {
      if (empty($data['body'])) {
        $data['body'] = '';
      } else {
        $data['body'] .= '\n';
      }
      foreach ($data['subtasks'] as $title => $task) {
        $data['body'] .= '[ ]' . $task . '\n';
      }
    }

    if (!empty($data['project'])) {
      $issue = $github->api('issue')->create($data['org'], $data['project'], array(
        'title' => $data['title'],
        'body' => $data['body'],
      ));

      if (!empty($labels)) {
        foreach ($data['labels'] as $label) {
          $labels = $gitub->api('issue')->labels()->add($data['org'], $data['project'], $issue, $label);
        }
      }

      return $github->api('org_projects')->columns()->cards()->create($data['column'], array('content_type' => 'Issue', 'content_id' => $issue));
    } else {
      return $github->api('org_projects')->columns()->cards()->create($data->data['column']);
    }
  }
}
