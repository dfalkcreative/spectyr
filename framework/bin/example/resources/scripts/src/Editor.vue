<template>
    <div class="editor">
        <md-card>
            <div id="overview-container"></div>

            <md-card-content>
                <div id="waveform-container">
                    <div id="zoomview-container"></div>
                </div>

                <div id="demo-controls">
                    <audio id="audio" controls="controls">
                        <source src="/resources/audio/track.mp3" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            </md-card-content>

            <md-card-actions>
                <md-button class="md-icon-button" @click="zoomIn">
                    <md-icon>zoom_in</md-icon>
                </md-button>

                <md-button class="md-icon-button" @click="zoomOut">
                    <md-icon>zoom_out</md-icon>
                </md-button>

                <md-button class="md-icon-button" @click="addSegment">
                    <md-icon>content_cut</md-icon>
                </md-button>
            </md-card-actions>
        </md-card>

        <div v-if="this.instance">
            <md-card v-for="segment in this.instance.segments.getSegments()">
                <div class="d-flex-justify">
                    <md-card-header>
                        <div class="md-subheading">{{ segment.labelText }}</div>
                        <div class="md-caption">{{ segment.startTime.toFixed(2) }}s - {{ segment.endTime.toFixed(2) }}s</div>
                    </md-card-header>

                    <md-card-actions>
                        <md-button class="md-icon-button">
                            <md-icon>search</md-icon>
                        </md-button>

                        <md-button class="md-icon-button">
                            <md-icon>play_arrow</md-icon>
                        </md-button>

                        <md-button class="md-icon-button">
                            <md-icon>delete</md-icon>
                        </md-button>
                    </md-card-actions>
                </div>
            </md-card>
        </div>
    </div>
</template>
<script>
    const Peaks = require('peaks.js');

    export default {
        data(){
            return {
                segments: 1,
                instance: null
            }
        },
        computed: {
            options(){
                return {
                    height: 100,
                    containers: {
                        zoomview: document.getElementById('zoomview-container'),
                        overview: document.getElementById('overview-container')
                    },
                    mediaElement: document.getElementById('audio'),
                    dataUri: {
                        arraybuffer: '/resources/audio/track.dat',
                        json: '/resources/audio/track.json'
                    },
                    keyboard: true,
                    pointMarkerColor: '#006eb0',
                    showPlayheadTime: true
                };
            }
        },
        components: {
            Peaks
        },
        methods: {
            zoomIn(){
                this.instance.zoom.zoomIn();
            },
            zoomOut(){
                this.instance.zoom.zoomOut();
            },
            addSegment(){
                this.instance.segments.add({
                    startTime: this.instance.player.getCurrentTime(),
                    endTime: this.instance.player.getCurrentTime() + 1,
                    labelText: 'Sound ' + this.segments++,
                    editable: true
                });
            },
            addPoint(){
                this.instance.points.add({
                    time: this.instance.player.getCurrentTime(),
                    labelText: 'Point',
                    editable: true
                });
            },
            seek(time){
                let seconds = parseFloat(time);

                if (!Number.isNaN(seconds)) {
                    this.instance.player.seek(seconds);
                }
            },
            resize(){
                let zoomviewContainer = document.getElementById('zoomview-container');
                let overviewContainer = document.getElementById('overview-container');

                let zoomviewStyle = zoomviewContainer.offsetHeight === 200 ? 'height:300px' : 'height:200px';
                let overviewStyle = overviewContainer.offsetHeight === 85  ? 'height:200px' : 'height:85px';

                zoomviewContainer.setAttribute('style', zoomviewStyle);
                overviewContainer.setAttribute('style', overviewStyle);

                let zoomview = this.instance.views.getView('zoomview');
                if (zoomview) {
                    zoomview.fitToContainer();
                }

                let overview = this.instance.views.getView('overview');
                if (overview) {
                    overview.fitToContainer();
                }
            }
        },
        mounted(){
            let root = this;

            Peaks.init(root.options, function(err, peaksInstance) {
                if (err) {
                    console.error(err.message);
                    return;
                }

                // Configure the editor instance.
                root.instance = peaksInstance;

                document.querySelector('body').addEventListener('click', function(event) {
                    var element = event.target;
                    var action  = element.getAttribute('data-action');
                    var id      = element.getAttribute('data-id');

                    if (action === 'play-segment') {
                        var segment = peaksInstance.segments.getSegment(id);
                        peaksInstance.player.playSegment(segment);
                    }
                    else if (action === 'loop-segment') {
                        var segment = peaksInstance.segments.getSegment(id);
                        peaksInstance.player.playSegment(segment, true);
                    }
                    else if (action === 'remove-point') {
                        peaksInstance.points.removeById(id);
                    }
                    else if (action === 'remove-segment') {
                        peaksInstance.segments.removeById(id);
                    }
                });

                var amplitudeScales = {
                    "0": 0.0,
                    "1": 0.1,
                    "2": 0.25,
                    "3": 0.5,
                    "4": 0.75,
                    "5": 1.0,
                    "6": 1.5,
                    "7": 2.0,
                    "8": 3.0,
                    "9": 4.0,
                    "10": 5.0
                };

                // Points mouse events
                peaksInstance.on('points.mouseenter', function(point) {
                    console.log('points.mouseenter:', point);
                });

                peaksInstance.on('points.mouseleave', function(point) {
                    console.log('points.mouseleave:', point);
                });

                peaksInstance.on('points.dblclick', function(point) {
                    console.log('points.dblclick:', point);
                });

                peaksInstance.on('points.dragstart', function(point) {
                    console.log('points.dragstart:', point);
                });

                peaksInstance.on('points.dragmove', function(point) {
                    console.log('points.dragmove:', point);
                });

                peaksInstance.on('points.dragend', function(point) {
                    console.log('points.dragend:', point);
                });

                // Segments mouse events
                peaksInstance.on('segments.dragstart', function(segment, startMarker) {
                    console.log('segments.dragstart:', segment, startMarker);
                });

                peaksInstance.on('segments.dragend', function(segment, startMarker) {
                    console.log('segments.dragend:', segment, startMarker);
                });

                peaksInstance.on('segments.dragged', function(segment, startMarker) {
                    console.log('segments.dragged:', segment, startMarker);
                });

                peaksInstance.on('segments.mouseenter', function(segment) {
                    console.log('segments.mouseenter:', segment);
                });

                peaksInstance.on('segments.mouseleave', function(segment) {
                    console.log('segments.mouseleave:', segment);
                });

                peaksInstance.on('segments.click', function(segment) {
                    console.log('segments.click:', segment);
                });

                peaksInstance.on('zoomview.dblclick', function(time) {
                    console.log('zoomview.dblclick:', time);
                });

                peaksInstance.on('overview.dblclick', function(time) {
                    console.log('overview.dblclick:', time);
                });

                peaksInstance.on('player.seeked', function(time) {
                    console.log('player.seeked:', time);
                });

                peaksInstance.on('player.play', function(time) {
                    console.log('player.play:', time);
                });

                peaksInstance.on('player.pause', function(time) {
                    console.log('player.pause:', time);
                });
            });

            console.log("Editor initialized.");
        }
    }
</script>
<style>
    #zoomview-container {
        /*box-shadow: 3px 3px 20px #919191;*/
        /*margin: 0 0 24px 0;*/
        /*-moz-box-shadow: 3px 3px 20px #919191;*/
        /*-webkit-box-shadow: 3px 3px 20px #919191;*/
        line-height: 0;
        height: 300px;
    }

    #zoomview-container .konvajs-content {
        width: 100% !important;
    }

    #overview-container{
        background-color: #f0f0f0;
    }

    #audio {
        flex: 0 0 30%;
    }

    #seek-time {
        width: 4em;
    }

    .editor .md-card{
        margin-bottom: 1em;
    }
</style>