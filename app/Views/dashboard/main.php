<h2>Resume</h2>

<div class="cards">
    <div class="dashboard-card">
        <div class="card-head">
            <div class="card-title">
                Total races and laps
            </div>
        </div>
        <div class="card-body">
            <table class="fullPage responsive">
                <thead>
                    <tr>
                        <th>Total races</th>
                        <th>Total laps</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-title="Total races">
                            <?= $total_races ?>
                        </td>
                        <td data-title="Total laps">
                            <?= $total_laps ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-head">
            <div class="card-title">
                Your 3 most used cars
            </div>
        </div>
        <div class="card-body">
            <table class="fullPage responsive">
                <thead>
                    <tr>
                        <th>Car</th>
                        <th>Total used</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($most_used_cars as $car): ?>
                        <tr>
                            <td data-title="Car">
                                <?= linkTag($car->car_id, 'car', $car->car_name) ?>
                            </td>
                            <td data-title="Total used">
                                <?= $car->total_used ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Most used track -->
    <div class="dashboard-card">
        <div class="card-head">
            <div class="card-title">
                Your 3 most used tracks
            </div>
        </div>
        <div class="card-body">
            <table class="fullPage responsive">
                <thead>
                    <tr>
                        <th>Track</th>
                        <th>Total used</th>
                        <th>Total laps</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($most_used_tracks as $track): ?>
                        <tr>
                            <td data-title="Track">
                                <?= linkTag($track->track_id, 'track', $track->track_name) ?>
                            </td>
                            <td data-title="Total used">
                                <?= $track->total_used ?>
                            </td>
                            <td data-title="Total used">
                                <?= $track->total_laps ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>