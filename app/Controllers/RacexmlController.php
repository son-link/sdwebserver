<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\RaceConfigModel;

class RacexmlController extends Controller
{
    public function weekly()
    {
        $model = new RaceConfigModel();
        $data = $model->getCurrentRaceConfig();

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE params SYSTEM "params.dtd">

<params name="Quick Race" version="1.26">
  <section name="Header">
    <attstr name="name" val="Quick Race"/>
    <attstr name="type" val="Quick"/>
    <attstr name="description" val="Quick Race"/>
    <attnum name="priority" val="90"/>
    <attstr name="menu image" val="data/img/splash-quickrace.jpg"/>
    <attstr name="run image" val="data/img/splash-run-practice.jpg"/>
  </section>

  <section name="Tracks">
    <attnum name="maximum number" val="1"/>
    <section name="1">
      <attstr name="name" val="{$data['track']}"/>
      <attstr name="category" val="{$data['category']}"/>
    </section>
  </section>

  <section name="Races">
    <section name="1">
      <attstr name="name" val="Quick Race"/>
    </section>
  </section>

  <section name="Quick Race">
    <attnum name="laps" val="{$data['laps']}"/>
    <attnum name="distance" unit="km" val="0"/>
    <attstr name="type" val="race"/>
    <attstr name="starting order" val="drivers list"/>
    <attstr name="restart" val="yes"/>
    <attnum name="sessiontime" unit="s" val="0"/>
    <attstr name="rain" val="{$data['rain']}"/>
    <attstr name="clouds" val="{$data['clouds']}"/>
    <attstr name="time of day" val="{$data['timeOfDay']}"/>
    <attstr name="season" val="{$data['season']}"/>
    <attstr name="weather" val="config"/>
    <attstr name="display mode" val="normal"/>
    <section name="Starting Grid">
      <attnum name="rows" val="2"/>
      <attnum name="distance to start" val="25"/>
      <attnum name="distance between columns" val="20"/>
      <attnum name="offset within a column" val="10"/>
      <attnum name="initial speed" val="0"/>
      <attnum name="initial height" val="0.2"/>
    </section>
  </section>

  <section name="Drivers">
    <attnum name="maximum number" val="40"/>
    <attstr name="focused module" val="human"/>
    <attnum name="focused idx" val="6"/>
    <attstr name="rejected types" val="networkhuman"/>
  </section>

  <section name="Configuration">
    <attnum name="current configuration" val="4"/>
    <section name="1">
      <attstr name="type" val="track select"/>
    </section>
    <section name="2">
      <attstr name="type" val="drivers select"/>
    </section>
    <section name="3">
      <attstr name="type" val="race config"/>
      <attstr name="race" val="Quick Race"/>
      <section name="Options">
        <section name="1">
          <attstr name="type" val="race length"/>
        </section>
        <section name="2">
          <attstr name="type" val="display mode"/>
        </section>
        <section name="3">
          <attstr name="type" val="time of day"/>
        </section>
        <section name="4">
          <attstr name="type" val="cloud cover"/>
        </section>
        <section name="5">
          <attstr name="type" val="rain fall"/>
        </section>
        <section name="6">
          <attstr name="type" val="season"/>
        </section>
        <section name="7">
          <attstr name="type" val="weather"/>
        </section>
      </section>
    </section>
  </section>

  <section name="Drivers Start List">
  </section>

</params>
XML;

        return $this->response
            ->setHeader('Content-Type', 'application/xml')
            ->setBody(ltrim($xml)); // removes leading newlines/whitespace
    }
}
