<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\Dimension;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    protected $analyticsDataClient;

    public function __construct()
    {
        $this->analyticsDataClient = new BetaAnalyticsDataClient();
    }

    public function fetchData(Request $request)
    {
        $propertyId = '460219113';
        $period = $request->input('period', 'today');
        $granularity = $request->input('granularity', 'day');
        $year = $request->input('year');

        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        // Initialize $startDate and $endDate based on the period
        if ($fromDate && $toDate) {
            $startDate = Carbon::parse($fromDate)->startOfDay();
            $endDate = Carbon::parse($toDate)->endOfDay();
        } else {
            switch ($period) {
                case 'today':
                    $endDate = Carbon::today();
                    $startDate = $endDate->copy();
                    break;
                case 'weekly':
                    $startDate = Carbon::now()->subDays(6);
                    $endDate = Carbon::now();
                    break;
                case 'monthly':
                    $endDate = Carbon::today();
                    $startDate = $endDate->copy()->subDays(29); // This will give us 30 days including today
                    break;
                case 'yearly':
                    $year = $year ?? Carbon::now()->year;
                    $startDate = Carbon::create($year)->startOfYear();
                    $endDate = Carbon::create($year)->endOfYear();
                    $granularity = 'month';
                    break;
                default:
                    return response()->json(['error' => 'Invalid period'], 400);
            }
        }

        if ($startDate->greaterThan($endDate)) {
            return response()->json(['error' => 'The start date must be before the end date.'], 400);
        }

        $dateRange = new DateRange([
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ]);

        $metrics = [
            new Metric(['name' => 'screenPageViews']),
            new Metric(['name' => 'sessions']),
            new Metric(['name' => 'totalUsers']),
            new Metric(['name' => 'bounceRate']),
            new Metric(['name' => 'userEngagementDuration']),
            new Metric(['name' => 'engagementRate']),
        ];

        try {
            $aggregateResponse = $this->analyticsDataClient->runReport([
                'property' => 'properties/' . $propertyId,
                'dateRanges' => [$dateRange],
                'metrics' => $metrics,
            ]);

            // Check if there are any rows in the response
            if ($aggregateResponse->getRowCount() > 0) {
                $totalEngagementTime = $aggregateResponse->getRows()[0]->getMetricValues()[4]->getValue();
                $totalUsers = $aggregateResponse->getRows()[0]->getMetricValues()[2]->getValue();
                $averageEngagementTimePerUser = $totalUsers > 0 ? ($totalEngagementTime / $totalUsers) : 0;
                $avgMinutes = floor($averageEngagementTimePerUser / 60);
                $avgSeconds = $averageEngagementTimePerUser % 60;

                $engagementRate = $aggregateResponse->getRows()[0]->getMetricValues()[5]->getValue();

                $metricsData = [
                    'views' => $aggregateResponse->getRows()[0]->getMetricValues()[0]->getValue(),
                    'visits' => $aggregateResponse->getRows()[0]->getMetricValues()[1]->getValue(),
                    'visitors' => $totalUsers,
                    'bounceRate' => number_format($aggregateResponse->getRows()[0]->getMetricValues()[3]->getValue() * 100, 2) . '%',
                    'avgVisitTime' => $avgMinutes . ' M ' . $avgSeconds . ' S',
                    'engagementRate' => number_format($engagementRate * 100, 2) . '%',
                ];
            } else {
                // If there are no rows, set default values
                $metricsData = [
                    'views' => 0,
                    'visits' => 0,
                    'visitors' => 0,
                    'bounceRate' => '0.00%',
                    'avgVisitTime' => '0 M 0 S',
                    'engagementRate' => '0.00%',
                ];
            }

            $dimensions = $granularity === 'hour'
                ? [new Dimension(['name' => 'hour'])]
                : ($granularity === 'month' ? [new Dimension(['name' => 'month'])] : [new Dimension(['name' => 'date'])]);

            $dailyResponse = $this->analyticsDataClient->runReport([
                'property' => 'properties/' . $propertyId,
                'dateRanges' => [$dateRange],
                'metrics' => [
                    new Metric(['name' => 'screenPageViews']),
                    new Metric(['name' => 'totalUsers']),
                    new Metric(['name' => 'engagementRate']),
                    new Metric(['name' => 'bounceRate']),
                ],
                'dimensions' => $dimensions,
            ]);

            if ($period === 'weekly' || $period === 'yearly') {
                $allPeriods = [];
                if ($period === 'weekly') {
                    for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
                        $key = $date->toDateString();
                        $allPeriods[$key] = [
                            'dimension' => $key,
                            'views' => 0,
                            'visitors' => 0,
                            'engagementRate' => '0.00',
                            'bounceRate' => '0.00',
                        ];
                    }
                } else { // yearly
                    $monthNames = [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];
                    foreach ($monthNames as $index => $month) {
                        $allPeriods[$month] = [
                            'dimension' => $month,
                            'views' => 0,
                            'visitors' => 0,
                            'engagementRate' => '0.00',
                            'bounceRate' => '0.00',
                        ];
                    }
                }

                foreach ($dailyResponse->getRows() as $row) {
                    if ($period === 'weekly') {
                        $key = Carbon::createFromFormat('Ymd', $row->getDimensionValues()[0]->getValue())->toDateString();
                    } else { // yearly
                        $monthNumber = (int)$row->getDimensionValues()[0]->getValue();
                        $key = $monthNames[$monthNumber - 1];
                    }

                    $allPeriods[$key] = [
                        'dimension' => $key,
                        'views' => $row->getMetricValues()[0]->getValue(),
                        'visitors' => $row->getMetricValues()[1]->getValue(),
                        'engagementRate' => number_format($row->getMetricValues()[2]->getValue() * 100, 2),
                        'bounceRate' => number_format($row->getMetricValues()[3]->getValue() * 100, 2),
                    ];
                }

                $dataArray = array_values($allPeriods);
            } elseif ($period === 'monthly') {
                $allDates = [];
                for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
                    $key = $date->toDateString();
                    $allDates[$key] = [
                        'dimension' => $key,
                        'views' => 0,
                        'visitors' => 0,
                        'engagementRate' => '0.00',
                        'bounceRate' => '0.00',
                    ];
                }

                foreach ($dailyResponse->getRows() as $row) {
                    $key = Carbon::createFromFormat('Ymd', $row->getDimensionValues()[0]->getValue())->toDateString();
                    if (isset($allDates[$key])) {
                        $allDates[$key] = [
                            'dimension' => $key,
                            'views' => $row->getMetricValues()[0]->getValue(),
                            'visitors' => $row->getMetricValues()[1]->getValue(),
                            'engagementRate' => number_format($row->getMetricValues()[2]->getValue() * 100, 2),
                            'bounceRate' => number_format($row->getMetricValues()[3]->getValue() * 100, 2),
                        ];
                    }
                }

                $dataArray = array_values($allDates);
            } else{
                if ($granularity === 'hour') {
                    $allHours = [];
                    for ($hour = 0; $hour < 24; $hour++) {
                        $allHours[sprintf('%02d', $hour)] = [
                            'dimension' => sprintf('%02d', $hour),
                            'views' => 0,
                            'visitors' => 0,
                            'engagementRate' => '0.00',
                            'bounceRate' => '0.00',
                        ];
                    }
            
                    foreach ($dailyResponse->getRows() as $row) {
                        $hour = $row->getDimensionValues()[0]->getValue();
                        if (isset($allHours[$hour])) {
                            $allHours[$hour] = [
                                'dimension' => $hour,
                                'views' => $row->getMetricValues()[0]->getValue(),
                                'visitors' => $row->getMetricValues()[1]->getValue(),
                                'engagementRate' => number_format($row->getMetricValues()[2]->getValue() * 100, 2),
                                'bounceRate' => number_format($row->getMetricValues()[3]->getValue() * 100, 2),
                            ];
                        }
                    }
            
                    $dataArray = array_values($allHours); // Flatten array for response
                } else {
                    $dataArray = [];
                    foreach ($dailyResponse->getRows() as $row) {
                        $dimensionValue = Carbon::createFromFormat('Ymd', $row->getDimensionValues()[0]->getValue())->toDateString();
                        $dataArray[] = [
                            'dimension' => $dimensionValue,
                            'views' => $row->getMetricValues()[0]->getValue(),
                            'visitors' => $row->getMetricValues()[1]->getValue(),
                            'engagementRate' => number_format($row->getMetricValues()[2]->getValue() * 100, 2),
                            'bounceRate' => number_format($row->getMetricValues()[3]->getValue() * 100, 2),
                        ];
                    }
                }
            }

            $userTypeResponse = $this->analyticsDataClient->runReport([
                'property' => 'properties/' . $propertyId,
                'dateRanges' => [$dateRange],
                'dimensions' => [new Dimension(['name' => 'newVsReturning'])],
                'metrics' => [new Metric(['name' => 'totalUsers'])],
            ]);

            $userTypes = [
                'new' => 0,
                'returning' => 0,
            ];

            foreach ($userTypeResponse->getRows() as $row) {
                $userType = $row->getDimensionValues()[0]->getValue();
                $userCount = $row->getMetricValues()[0]->getValue();

                if ($userType === 'new') {
                    $userTypes['new'] = $userCount;
                } elseif ($userType === 'returning') {
                    $userTypes['returning'] = $userCount;
                }
            }

            return response()->json([
                'metricsData' => $metricsData,
                'dailyData' => $dataArray,
                'userTypes' => $userTypes,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

