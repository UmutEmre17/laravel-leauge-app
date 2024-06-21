<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Styles -->
</head>
<body class="antialiased">
    <div class="container">
        <h3 class="center">
            <b>{{ $group->name }}</b>
        </h3>
        
        <table class="centered">
            <thead>
                <tr>
                    <th>Team</th>
                    <th>Matches played</th>
                    <th>Win</th>
                    <th>Draw</th>
                    <th>Lose</th>
                    <th>Goals for</th>
                    <th>Goals against</th>
                    <th>Goals Difference</th>
                    <th>Points</th>
                    @if ($group->teams->first()->matches_played >= 3)
                        <th>Championship Probability</th>
                     @endif    
                </tr>
            </thead>
            <tbody>
                @foreach($group->teams as $team)
                    <tr>
                        <td>{{ $team->name }}</td>
                        <td>{{ $team->matches_played }}</td>
                        <td>{{ $team->win }}</td>
                        <td>{{ $team->draw }}</td>
                        <td>{{ $team->lose }}</td>
                        <td>{{ $team->goals_for }}</td>
                        <td>{{ $team->goals_against }}</td>
                        <td>{{ $team->goals_difference }}</td>
                        <td>{{ $team->pts }}</td>
                        @if ($team->matches_played >= 3)
                            <td>{{ $team->cmp_prob }}%</td>
                        @endif    
                    </tr>
                @endforeach
            </tbody>
        </table>

        <form action="{{ route('play-matches') }}" method="POST">
            @csrf
            <button type="submit">Play Matches</button>
        </form>
        
        <form action="{{ route('play-all-remaining-games', ['group' => $group->id]) }}" method="POST">
            @csrf
            <button type="submit">Play All Remaining Games</button>
        </form>

        <form action="{{ route('reset') }}" method="POST" style="margin-top: 20px;">
            @csrf
            <button type="submit">Reset</button>
        </form>

        @if($group->teams->first()->matches_played >= 1)
            
            <h4> Match Results:</h4>
            <ul>
            @if(session('matches'))
                <select name="selected_number" id="selected_number" style="display: block;">
                    <option value="{{ 0 }}">{{ 'Select a matchweek to display results' }}</option>
                    @for($i = 1; $i <= count($matches) / 2; $i++)
                        <option value="{{ $i  }}">{{ $i }}</option>
                    @endfor
                </select>
            @endif    
            
            <div id="match_details"></div>
            
            <script>
                document.getElementById('selected_number').addEventListener('change', function() {
                    var selectedNumber = this.value;
                    var matchDetails = document.getElementById('match_details');
                    var matches = <?php echo json_encode($matches);?>;

                    matchDetails.innerHTML = '<h5> Week ' + selectedNumber + ' results: </h5>';
                    matchDetails.innerHTML += '<p>' + matches[(selectedNumber - 1) * 2] + '</p>';
                    matchDetails.innerHTML += '<p>' + matches[(selectedNumber - 1) * 2 + 1] + '</p>';
                });
            </script>
        @endif    
      

    </div>
</body>
</html>
