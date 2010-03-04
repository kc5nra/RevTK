<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Flashcard Management
 * 
 * @package    RevTK
 * @subpackage Manage
 * @author     Fabrice Denis
 */

class manageActions extends coreActions
{
	const
		/**
		 * Name of the remove flashcards list selection.
		 */
		REMOVE_FLASHCARDS = 'removeFlashcards';
	
	public function executeIndex($request)
	{
	}
	
	public function executeAddorder($request)
	{
		// handle ajax requests (POST)
		if ($request->getMethod()===coreRequest::POST)
		{
			return $this->renderPartial('AddOrder');
		}
	}

	public function executeAddOrderConfirm($request)
	{
		$validator = new coreValidator('AddOrder');
		if ($validator->validate($request->getParameterHolder()->getAll()))
		{
			// create a Heisig flashcard selection
			$fcSel = new rtkFlashcardSelection($request);		
			if ($fcSel->addHeisigRange($this->getUser()->getUserId(), $request->getParameter('txtSelection'))!==false)
			{
				// store valid selection in session
				$newCards = $fcSel->getCards();
				if (count($newCards))
				{
					$this->getUser()->setAttribute('selection', serialize($newCards));
				}

				return $this->renderPartial('AddOrderConfirm', array(
					'newCards' => $newCards,
					'countNewCards' => count($newCards)
				));
			}
			else
			{
				$request->setError('x', 'Invalid selection.');
			}
		}
		
		$this->forward('manage', 'addorder');
	}

	public function executeAddOrderProcess($request)
	{
		// cancel action
		$this->forwardIf($request->hasParameter('cancel'), 'manage', 'addorder');

		// reset: restart form with cleared values
		if ($request->hasParameter('reset'))
		{
			$request->setParameter('txtSelection', '');
			$this->forward('manage', 'addorder');
		}

		// get validated selection from session
		$selection = $this->getUser()->getAttribute('selection');
		$this->forwardIf(!$selection, 'manage', 'addorder');
		$this->getUser()->getAttributeHolder()->remove('selection');
		$newCards = unserialize($selection);

		$cards = rtkFlashcardDeck::addSelection($this->getUser()->getUserId(), $newCards);
		if (count($cards) != count($newCards))
		{
			$request->setError('x', 'Oops! An error occured while adding flashcards, not all flashcards could be added.');
	  }

		return $this->renderPartial('AddOrderProcess', array(
			'cards' => $cards,
			'count' => count($cards)
		));
	}

	public function executeAddcustom($request)
	{
		// handle ajax requests (POST)
		if ($request->getMethod()===coreRequest::POST)
		{
			return $this->renderPartial('AddCustom');
		}
	}

	public function executeAddCustomConfirm($request)
	{
		$validator = new coreValidator('AddCustom');
		if ($validator->validate($request->getParameterHolder()->getAll()))
		{
			// create flashcard selection from string		
			$fcSel = new rtkFlashcardSelection($request);		
			if ($fcSel->setFromString($request->getParameter('txtSelection')))
			{
				$newCards = rtkFlashcardDeck::checkSelection($this->getUser()->getUserId(), $fcSel->getCards());
				$countNewCards = count($newCards);
				$countExistCards = $fcSel->getNumCards() - $countNewCards;
				
				// store valid selection in session
				if (count($newCards))
				{
					$this->getUser()->setAttribute('selection', serialize($newCards));
				}

				return $this->renderPartial('AddCustomConfirm', array(
					'newCards' => $newCards,
					'countNewCards' => $countNewCards,
					'countExistCards' => $countExistCards
				));
			}
			else
			{
				$request->setError('x', 'Invalid selection.');
			}
		}
		
		$this->forward('manage', 'addcustom');
	}
	
	public function executeAddCustomProcess($request)
	{
		// cancel: go back to edited form
		$this->forwardIf($request->hasParameter('cancel'), 'manage', 'addcustom');

		// reset: restart form with cleared values
		if ($request->hasParameter('reset'))
		{
			$request->setParameter('txtSelection', '');
			$this->forward('manage', 'addcustom');
		}

		// get validated selection from session
		$selection = $this->getUser()->getAttribute('selection');
		$this->forwardIf(!$selection, 'manage', 'addcustom');
		$this->getUser()->getAttributeHolder()->remove('selection');
		$newCards = unserialize($selection);

		$cards = rtkFlashcardDeck::addSelection($this->getUser()->getUserId(), $newCards);
		if (count($cards) != count($newCards))
		{
			$request->setError('x', 'Oops! An error occured while adding flashcards, not all flashcards could be added.');
	  }
		
		return $this->renderPartial('AddCustomProcess', array(
			'cards' => $cards,
			'count' => count($cards)
		));
	}

	public function executeRemovelist($request)
	{
		// handle ajax requests (POST)
		if ($request->getMethod()===coreRequest::POST)
		{
			// reset: restart form with empty selection
			if ($request->hasParameter('reset'))
			{
				uiSelectionState::clearSelection(self::REMOVE_FLASHCARDS);
			}
	
			return $this->renderPartial('RemoveList');
		}
		else
		{
			// reset selection on page load
			uiSelectionState::clearSelection(self::REMOVE_FLASHCARDS);			
		}
	}

	public function executeRemoveListTable($request)
	{
		uiSelectionState::updateSelection(self::REMOVE_FLASHCARDS, 'rf', $request->getParameterHolder()->getAll());

		return $this->renderComponent('manage', 'RemoveListTable');
	}

	public function executeRemoveListConfirm($request)
	{
		// Clear selection > reset form
		$this->forwardIf($request->hasParameter('reset'), 'manage', 'removelist');

		uiSelectionState::updateSelection(self::REMOVE_FLASHCARDS, 'rf', $request->getParameterHolder()->getAll());

		$cards = uiSelectionState::getSelection(self::REMOVE_FLASHCARDS)->getAll();
		
		return $this->renderPartial('RemoveListConfirm', array(
			'cards' => $cards,
			'count' => count($cards)
		));
	}

	public function executeRemoveListProcess($request)
	{
		// Confirm > cancel > go back and keep the current selection
		$this->forwardIf($request->hasParameter('cancel'), 'manage', 'removelist');
		// Process > continue > reset form
		$this->forwardIf($request->hasParameter('reset'), 'manage', 'removelist');

		// delete selected flashcards
		$selectedCards = uiSelectionState::getSelection(self::REMOVE_FLASHCARDS)->getAll();
		
		$cards = rtkFlashcardDeck::deleteSelection($this->getUser()->getUserId(), $selectedCards);
		if (count($cards) != count($selectedCards))
		{
			$request->setError('x', 'Oops! An error occured while deleting flashcards, not all flashcards were deleted.');
	  }

		return $this->renderPartial('RemoveListProcess', array(
			'cards' => $cards,
			'count' => count($cards)
		));
	}

	public function executeRemovecustom($request)
	{
		// handle ajax requests (POST)
		if ($request->getMethod()===coreRequest::POST)
		{
			return $this->renderPartial('RemoveCustom');
		}
	}

	public function executeRemoveCustomConfirm($request)
	{
		$validator = new coreValidator('RemoveCustom');
		if ($validator->validate($request->getParameterHolder()->getAll()))
		{
			// create flashcard selection from string		
			$fcSel = new rtkFlashcardSelection($request);		
			if ($fcSel->setFromString($request->getParameter('txtSelection')))
			{
				// store valid selection in session
				$cards = $fcSel->getCards();
				if (count($cards))
				{
					$this->getUser()->setAttribute('selection', serialize($cards));
				}

				return $this->renderPartial('RemoveCustomConfirm', array(
					'cards' => $cards,
					'count' => count($cards)
				));
			}
			else
			{
				$request->setError('x', 'Invalid selection.');
			}
		}
		
		$this->forward('manage', 'removecustom');
	}
	
	public function executeRemoveCustomProcess($request)
	{
		// cancel: go back to edited form
		$this->forwardIf($request->hasParameter('cancel'), 'manage', 'removecustom');

		// reset: restart form with cleared values
		if ($request->hasParameter('reset'))
		{
			$request->setParameter('txtSelection', '');
			$this->forward('manage', 'removecustom');
		}

		// delete selected flashcards
		$selection = $this->getUser()->getAttribute('selection');
		$this->forwardIf(!$selection, 'manage', 'removecustom');
		$this->getUser()->getAttributeHolder()->remove('selection');
		$selectedCards = unserialize($selection);

		$cards = rtkFlashcardDeck::deleteSelection($this->getUser()->getUserId(), $selectedCards);
		if (count($cards) != count($selectedCards))
		{
			$request->setError('x', 'Oops! An error occured while deleting flashcards, not all flashcards were deleted.');
	  }

		return $this->renderPartial('RemoveCustomProcess', array(
			'cards' => $cards,
			'count' => count($cards)
		));
	}
	
	/**
	 * Export the user's flaschards with their review status.
	 * 
	 */
	public function executeExport()
	{
	}
	
  public function executeExportflashcards()
  {
    $throttler = new RequestThrottler($this->getUser(), 'export');
    if (!$throttler->isValid()) {
      return $this->renderPartial('misc/requestThrottleError');
    }

    $csv = new ExportCSV($this->getContext()->getDatabase());
    $select = ReviewsPeer::getSelectForExport($this->getUser()->getUserId());
    $csvText = $csv->export($select,
      // column names
      array('FrameNumber', 'Kanji', 'Keyword', 'LastReview', 'ExpireDate', 'LeitnerBox', 'FailCount', 'PassCount'),
      // options
      array('col_escape' => array(0, 1, 1, 0, 0, 0, 0, 0))
    );
    
    $throttler->setTimeout();

    $this->getResponse()->setFileAttachmentHeaders('rtk_flashcards.csv');

    $this->setLayout(false);

    return $this->renderText($csvText);
  }	
}
