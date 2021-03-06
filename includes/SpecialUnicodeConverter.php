<?php

class SpecialUnicodeConverter extends SpecialPage {
	public function __construct() {
		parent::__construct( "UnicodeConverter" );
	}

	public function execute( $par ) {
		$this->setHeaders();

		$q = $this->getRequest()->getText( 'q' );
		$encQ = htmlspecialchars( $q );

		$formDescriptor = [
			'textarea' => [
				'type' => 'textarea',
				'name' => 'q',
				'default' => $encQ,
				'rows' => 15,
			]
		];

		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );
		$htmlForm
			->setName( 'ucf' )
			->setSubmitName( 'submit' )
			->setSubmitTextMsg( 'unicodeconverter-ok' )
			->prepareForm()
			->displayForm( false );

		if ( $q !== null ) {
			$html = $this->utf8ToHTML( htmlspecialchars( $q ) );
			$this->getOutput()->addHTML(
				"<br /><b>" .
				$this->msg( 'unicodeconverter-oldtext' )->escaped() .
				"</b><br /><br />" .
				nl2br( $html ) .
				"<br /><br /><hr /><br /><b>" .
				$this->msg( 'unicodeconverter-newtext' )->escaped() .
				"</b><br /><br />" .
				nl2br( htmlspecialchars( $html ) ) .
				"<br /><br />"
			);
		}
	}

	/**
	 * Converts a single UTF-8 character into the corresponding HTML character entity
	 * @param array $matches
	 * @return string
	 */
	private function utf8Entity( $matches ) {
		$char = $matches[0];
		// Find the length
		$z = ord( $char[0] );
		if ( $z & 0x80 ) {
			$length = 0;
			while ( $z & 0x80 ) {
				$length++;
				$z <<= 1;
			}
		} else {
			$length = 1;
		}

		if ( $length != strlen( $char ) ) {
			return '';
		}
		if ( $length === 1 ) {
			return $char;
		}

		// Mask off the length-determining bits and shift back to the original location
		$z &= 0xff;
		$z >>= $length;

		// Add in the free bits from subsequent bytes
		for ( $i = 1; $i < $length; $i++ ) {
			$z <<= 6;
			$z |= ord( $char[$i] ) & 0x3f;
		}

		// Make entity
		return "&#$z;";
	}

	/**
	 * Converts all multi-byte characters in a UTF-8 string into the appropriate
	 * character entity
	 * @param string $string
	 * @return string
	 */
	private function utf8ToHTML( $string ) {
		return preg_replace_callback(
			'/[\\xc0-\\xfd][\\x80-\\xbf]*/',
			[ $this, 'utf8Entity' ],
			$string
		);
	}
}
