

var tictactoe = {
	
	current_player: 1,
	
	thread: undefined,
	
	init: function()
	{
		console.log('init tictactoe');
		tictactoe.bindCases();
		tictactoe.startRefresh();
	},
	
	bindCases: function()
	{
		$('.grid-item').click(function () {
			$case = $(this);
			
			if ($case.hasClass('grid-value_-')) {
				$coords = $case.attr('id').split('-');
				$line = parseInt($coords[1]);
				$col = parseInt($coords[2]);
				tictactoe.caseClicked($line, $col);
			}
		});
	},
	
	/**
	 * Called when an empty case has been clicked
	 */
	caseClicked: function(line, col)
	{
		console.log('case clicked '+line+' ; '+col);
		tictactoe.set(line, col, tictactoe.current_player == 1 ? 'X' : 'O');
		tictactoe.tick(line, col);
		tictactoe.changeCurrentPlayer();
	},
	
	getCase: function(line, col)
	{
		return $(['#grid', line, col].join('-'));
	},
	
	set: function(line, col, value)
	{
		if (line < 0 || line > 2 || col < 0 || col > 2) {
			console.warn('tictactoe.set must have line and col between 0 and 2, got '+line+' ; '+col);
			return false;
		}
		
		if (['-', 'X', 'O'].indexOf(value) < 0) {
			console.warn('tictactoe.set must be X, O or -, got "'+value+'"');
			return false;
		}
		
		tictactoe.getCase(line, col)
			.removeClass('grid-value_-')
			.removeClass('grid-value_X')
			.removeClass('grid-value_O')
			.addClass('grid-value_'+value)
		;
		
		return true;
	},
	
	get: function(line, col)
	{
		var $case = tictactoe.getCase(line, col);

		if ($case.hasClass('grid-value_-')) return '-';
		if ($case.hasClass('grid-value_X')) return 'X';
		if ($case.hasClass('grid-value_O')) return 'O';
		
		console.warn('warn : grid in '+line+' ; '+col+' has no symbol class, "-" returned');
		return '-';
	},
	
	setGrid: function(grid)
	{
		if (grid.length != 9) {
			console.warn('tictactoe.setGrid, grid does not contains 9 cases : "'+grid+'"');
			return false;
		}
		
		for (var i = 0 ; i < 9 ; i++) {
			var line = Math.floor(i / 3);
			var col = i % 3;
			var value = grid.charAt(i);
			
			tictactoe.set(line, col, value);
		}
		
		return true;
	},
	
	getGrid: function()
	{
		var grid = '';
		
		for (var i = 0 ; i < 9 ; i++) {
			var line = Math.floor(i / 3);
			var col = i % 3;
			
			grid += tictactoe.get(line, col);
		}
		
		return grid;
	},
	
	changeCurrentPlayer: function()
	{
		tictactoe.current_player = 3 - tictactoe.current_player;
	},
	
	startRefresh: function()
	{
		if (tictactoe.thread) {
			clearInterval(tictactoe.thread);
		}
		
		tictactoe.thread = setInterval(tictactoe.refresh, 2000);
	},
	
	stopRefresh: function()
	{
		if (tictactoe.thread) {
			clearInterval(tictactoe.thread);
			tictactoe.thread = undefined;
		}
	},
	
	refresh: function()
	{
		phax.action('tictactoe', 'refresh', {extended_party_id: jsContext.extended_party.id});
	},
	
	refreshAction: function(r)
	{
		tictactoe.setGrid(r.party.grid);
		tictactoe.current_player = r.party.current_player;
	},
	
	tick: function(line, col)
	{
		var data = {
			locale: jsContext.locale,
			party_slug: jsContext.core_party.slug,
			extended_party_id: jsContext.extended_party.id,
			coords: {
				line: line,
				col: col
			}
		};
		
		phax.action('tictactoe', 'tick', data);
	},
	
	tickAction: function(r)
	{
		console.log(r);
	}
	
};

jQuery(function() {
	phax.load_controller('tictactoe');
});

