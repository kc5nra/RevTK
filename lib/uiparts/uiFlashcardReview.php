<?php
/**
 * uiFlashcardReview handles server-side logic for the uiFlashcardReview front end component.
 * 
 * This is a generic class that handles JSON requests to return flashcard data to the client,
 * and to update the database with data coming from the client.
 * The type of data on the flashcards, the handling of review status and review updates are
 * all managed by callback functions.
 * 
 * The only fixed information is that each flashcard must have a unique ID and this id
 * is the one that identifies flashcards between the server and client.
 * 
 * Options (constructor):
 * 
 *   items              Array     Array of flashcard ids
 *   ajax_url           String    Url of action to handle requests (eg. $this->getController()->genUrl(...))
 *   fn_get_flashcard   Callback  A php callback: function, array($obj, 'method') or array('class', 'staticmethod')
 *   fn_put_flashcard   Callback  ... (OPTIONAL)
 *
 * Callbacks:
 * 
 *   fn_get_flashcard($id)   
 *   					Returns flashcard data, must sanitize id, return data as associative array or object,
 *                      must return null if the data can not be retrieved (id invalid, ...).
 *   fn_put_flashcard($id, $data) 
 *                      Update the flashcard status, and anything else based on data received from client.
 *                      $id is the same as $data->id, and must be sanitized.
 *                      Returns false if the update was not succesfull.
 *  
 * Methods:
 * 
 *	 getPartialName()     Returns the partial name as passed to the constructor.
 *   getPartialVars()     Returns the options array as passed to the constructor, each option
 *                        becomes a variable in the partial.
 *
 *	 handleJsonRequest($oJson)
 *
 * Format of JSON request:
 * 
 *   get                  An array of item ids
 *                        [1, 2, 3, ...]
 *   put                  An array of flashcard update data as objects, each object has "id" property.
 *                        [ {id: 1, ... }, { id:2, ... }, ... ]
 *
 * Format of JSON response:
 * 
 *   get                  An array of flashcard data as objects
 *   put                  If there was a "put" request, returns the number of handled items as integer.
 *   
 *
 * Usage:
 * 	 
 *   Create an instance in the action:
 *   =>  $this->uiFR = new uiFlashcardReview('module/partial', array(...))
 *     
 *   Incude the partial in the action view, the partial has access to all
 *   the options passed to the constructor:
 *   =>  include_partial($uiFR->getPartialName(), $uiFR->getPartialVars())
 *
 *
 * @todo    Refactor generic, configure peer class for getFlashcardData() and peer class for putFlashcardData()
 *
 * @author  Fabrice Denis
 */

class uiFlashcardReview
{
	protected
		$options     = null,
		$partialName = null;

	/**
	 * Do not allow client to prefetch too many cards at once.
	 */
	const MAX_PREFETCH = 20;

	/**
	 * 
	 * Options
	 * 	  partial_name      The full partial name (module/partial) to use for rendering
	 * 
	 * @return 
	 */	
	public function __construct($options = array())
	{
		$this->options = (object) $options;
		$this->partialName = $this->options->partial_name;

		$this->user_id = coreContext::getInstance()->getUser()->getUserId();
		
		//testing
		/*
		if (isset($this->options->items))
		{
			$x = array();
			for ($i = 1; $i<=3007; $i++) {
				$x[] = 3008 - $i;
			}
			$this->options->items = $x; //array_slice($this->options->items, 0, 4);
		}
		*/
	}

	/**
	 * Handles JSON request and returns a JSON response
	 * 
	 * @param object  JSON request as a native php object (stdClass)
	 * 
	 * @return        JSON response as a string
	 */
	public function handleJsonRequest($oJson)
	{
		$oResponse = new stdClass;
		
		// get flashcard data
		if (isset($oJson->get) && is_array($oJson->get))
		{
			$get_cards = array();
			
			// do not accept too large prefetch (tampering with ajax request on client)
			if (count($oJson->get) > self::MAX_PREFETCH) {
				$oJson->get = array_slice($oJson->get, 0, self::MAX_PREFETCH);
			}
			
			foreach ($oJson->get as $id)
			{
				$cardData = call_user_func($this->options->fn_get_flashcard, $id);
				
				if ($cardData === null) {
					throw new rtkAjaxException('Could not fetch item "'.$id.'" in JSON request');
				}
				$get_cards[] = $cardData;
			}
			
			if (count($get_cards)) {
				$oResponse->get = $get_cards;
			}
		}

		// update flashcard reviews
		if (isset($oJson->put) && is_array($oJson->put))
		{
			$putSuccess = 0;
			
			if (isset($this->options->fn_put_flashcard))
			{
				foreach ($oJson->put as $oAnswer)
				{
					if (!call_user_func($this->options->fn_put_flashcard, $oAnswer->id, $oAnswer))
					{
						// must stop at first error, cf. client side code (js) clearing of cached answers
						break;
					}
					$putSuccess++;
				}
			}
			
			$oResponse->put = $putSuccess;
		}

		return coreJson::encode($oResponse);
	}
	
	/**
	 * Returns the partial name (module/action) for the include_partial() helper
	 * to include this component in the action view.
	 * 
	 * @return string
	 */
	public function getPartialName()
	{
		return $this->partialName;
	}
	
	/**
	 * Returns the component's constructor options (and any options added later)
	 * as a variable array for the partial include.
	 * 
	 * @return array
	 */
	public function getPartialVars()
	{
		return $this->options;
	}
}
