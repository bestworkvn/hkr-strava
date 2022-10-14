<x-layout>
    <x-setting heading="Activitiy detail">
        <x-panel>
            <h1 class="font-medium leading-tight text-base mt-0 mb-2 text-black-600">Title: {{ $tracklog->name }}</h1>
            <h6 class="font-medium leading-tight text-base mt-0 mb-2 text-grey-600">Distance: {{ round($tracklog->distance/1000,2) }}</h6>
            <h6 class="font-medium leading-tight text-base mt-0 mb-2 text-grey-600">Time: {{ gmdate('H:i:s', $tracklog->moving_time) }}</h6>
            <h6 class="font-medium leading-tight text-base mt-0 mb-2 text-grey-600">Map</h6>
            <div id="map-canvas"></div>
        </x-panel>
    </x-setting>
</x-layout>
