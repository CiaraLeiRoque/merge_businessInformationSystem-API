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

        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        // Initialize $startDate and $endDate based on the period
        if ($fromDate && $toDate) {
            // If custom dates are provided, use them
            $startDate = Carbon::parse($fromDate)->startOfDay();
            $endDate = Carbon::parse($toDate)->endOfDay();
        } else {
            // Otherwise, set default dates based on the period
            switch ($period) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today();
                    break;

                case 'weekly':
                    $startDate = Carbon::now()->subDays(7);
                    $endDate = Carbon::now();
                    break;

                case 'monthly':
                    $startDate = Carbon::now()->subDays(30);
                    $endDate = Carbon::now();
                    break;

                case 'yearly':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    $granularity = 'month'; // Adjust granularity for yearly
                    break;

                default:
                    return response()->json(['error' => 'Invalid period'], 400);
            }
        }

        // Ensure the start date is before the end date
        if ($startDate->greaterThan($endDate)) {
            return response()->json(['error' => 'The start date must be before the end date.'], 400);
        }

        $dateRange = new DateRange([
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ]);



        $dateRange = new DateRange([
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ]);

        

        $dateRange = new DateRange([
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ]);

        // Request 1: Aggregate data for top metrics
        $metrics = [
            new Metric(['name' => 'screenPageViews']),
            new Metric(['name' => 'sessions']),
            new Metric(['name' => 'totalUsers']),
            new Metric(['name' => 'bounceRate']),
            new Metric(['name' => 'userEngagementDuration']),
            new Metric(['name' => 'engagementRate']),
        ];

        $aggregateResponse = $this->analyticsDataClient->runReport([
            'property' => 'properties/' . $propertyId,
            'dateRanges' => [$dateRange],
            'metrics' => $metrics,
        ]);

        // Calculate average engagement time for top metrics
        $totalEngagementTime = $aggregateResponse->getRows()[0]->getMetricValues()[4]->getValue(); // in seconds
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

        // Request 2: Daily breakdown for views and visitors (for the chart)
        $dimensions = $granularity === 'hour'
            ? [new Dimension(['name' => 'hour'])]
            : ($granularity === 'month' ? [new Dimension(['name' => 'month'])] : [new Dimension(['name' => 'date'])]);

        $dailyResponse = $this->analyticsDataClient->runReport([
            'property' => 'properties/' . $propertyId,
            'dateRanges' => [$dateRange],
            'metrics' => 
            [
                new Metric(['name' => 'screenPageViews']), 
                new Metric(['name' => 'totalUsers']), 
                new Metric(['name' => 'engagementRate']),
                new Metric(['name' => 'bounceRate']),
            ],
            'dimensions' => $dimensions,
        ]);

        $dataArray = [];
        foreach ($dailyResponse->getRows() as $row) {
            $dimensionValue = $granularity === 'hour' 
                ? $row->getDimensionValues()[0]->getValue() 
                : ($granularity === 'month' 
                    ? Carbon::create(null, $row->getDimensionValues()[0]->getValue())->format('Y-m')
                    : Carbon::createFromFormat('Ymd', $row->getDimensionValues()[0]->getValue())->toDateString());

            $dataArray[] = [
                'dimension' => $dimensionValue,
                'views' => $row->getMetricValues()[0]->getValue(),
                'visitors' => $row->getMetricValues()[1]->getValue(),
                'engagementRate' => number_format($row->getMetricValues()[2]->getValue() * 100, 2),
                'bounceRate' => number_format($row->getMetricValues()[3]->getValue() * 100, 2),
            ];
        }

        // Generate complete dimension range
        if ($granularity === 'hour') {
            $dimensionsArray = range(0, 23); // 00:00 to 23:00
        } elseif ($granularity === 'month') {
            $dimensionsArray = array_map(fn($month) => Carbon::create($startDate->year, $month)->format('Y-m'), range(1, 12));
        } else {
            $datePeriod = new \DatePeriod(
                new \DateTime($startDate->toDateString()),
                new \DateInterval('P1D'),
                (new \DateTime($endDate->toDateString()))->modify('+1 day')
            );
            $dimensionsArray = array_map(fn($date) => $date->format('Y-m-d'), iterator_to_array($datePeriod));
        }

        // Prepare data with defaults
        $completeData = array_map(fn($dim) => [
            'dimension' => $dim,
            'views' => 0,
            'visitors' => 0,
            'engagementRate' => '0.00',
            'bounceRate' => '0.00',
        ], $dimensionsArray);

        // Merge API data into complete data
        foreach ($dataArray as $row) {
            $index = array_search($row['dimension'], array_column($completeData, 'dimension'));
            if ($index !== false) {
                $completeData[$index] = $row;
            }
        }

        $dataArray = $completeData; // Use complete data in response

        // Request 3: Breakdown of returning vs. new users
        $userTypeResponse = $this->analyticsDataClient->runReport([
            'property' => 'properties/' . $propertyId,
            'dateRanges' => [$dateRange],
            'dimensions' => [
                new Dimension(['name' => 'newVsReturning']),
            ],
            'metrics' => [
                new Metric(['name' => 'totalUsers']),
            ],
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

        // Include both total metrics and daily data in the response
        return response()->json([
            'metricsData' => $metricsData,
            'dailyData' => $dataArray,
            'userTypes' => $userTypes,
        ]);
    }
}