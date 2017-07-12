<?php

  namespace Drupal\casestudies\form;

  use Drupal\Core\Form\FormBase;
  use Drupal\Core\Form\FormStateInterface;
  use \Drupal\node\Entity\Node;
  use \Drupal\file\Entity\File;

  include_once('sites/all/libraries/simplehtmldom/simple_html_dom.php');

  /**
   * Implements a Case Studies form
   */
   class CaseStudiesForm extends FormBase{

      // Returns a string that is the unique id of your form.
      public function getFormId(){
        return 'casestudies_form';
      }

      public function buildForm(array $form, FormStateInterface $form_state){
        $header = [
          'title' => $this->t('Title'),
          'body' => $this->t('Body'),
          'success' => $this->t("Success"),
        ];

        $html = file_get_html('https://www.achieveinternet.com/case-studies');
        $case_study_query = 'div.views-field-field-featured-case-study-image a[href*="/portfolio/"]';


        $options = [];
        foreach($html -> find($case_study_query) as $case_study_tag){
          $case_study_url = 'https://www.achieveinternet.com' . $case_study_tag->href;
          $case_html = file_get_html($case_study_url);

          // $case_title = $case_html->find('div#md1',0) ?: "Title Not Found"
          $case_title = $case_html->find('div#md1',0);
          if(isset($case_title)){
            $case_title = ($case_title->find('h2',0))->plaintext;
          } else{
            $case_title = 'Title Not Found';
          }

          // $case_body = $case_html->find('div#md2', 0;) ?: "Body Not Found"
          $case_body = $case_html->find('div#md2', 0);
          if(isset($case_body)){
            $body_builder = array();
            foreach($case_body->find('h4, p') as $next){
              $body_builder[] = $next->plaintext;
            };
            $case_body = implode(' ', $body_builder);
          } else {
            $case_body = 'Body Not Found';
          }

          // $case_success = $case_html->find("div#md3, 0") ?: "Success Not Found"
          $case_success = $case_html->find('div#md3', 0);
          if(isset($case_success)){
            $success_builder = array();
            foreach($case_success->find('h4, p') as $next){
              $success_builder[] = $next->plaintext;
            };
            $case_success = implode(' ', $success_builder);
          } else {
            $case_success = 'Success Not Found';
          }

          $options[] = array(
            'title' => $case_title,
            'body' => $case_body,
            'success' => $case_success,
          );
        };

        $form['table'] = array(
          '#type' => 'tableselect',
          '#header' => $header,
          '#options' => $options,
          '#empty' => $this->t('Table has no options'),
        );

        $form['submit'] = array(
          '#type' => 'submit',
          '#value' => $this->t('Submit'),
        );

        return $form;
      }


      // Have this function grab all items that are selected from the form and create
      // content (nodes) with form data in the corresponding fields.
      // - Build the content type first through the UI
      // - Hint: $form_state will tell you which rows are selected
       public function submitForm(array &$form, FormStateInterface $form_state){
          $rows = $form['table']['#options'];
          $row_keys = $form_state->getValue('table');
          $opt_index = 0;
          foreach($rows as $row){
            if($row_keys[$opt_index]){
              $node = Node::create([
                'type'        => 'case_study_node',
                'title'       => $row['title'],
                'body'        => $row['body'] . $row['success'],
              ]);
              $node->save();
            }
            $opt_index += 1;
          };
          drupal_set_message('Created nodes hyperlul XD', TRUE);
        }
    }
