#!/usr/bin/env python3
# this python script copy all
# default car previews
# tracks outlines
# from the tgiven svn repo to a specified folder in the webserver

# todo: check if directories exist: "cars" and "tracks" in "img",
# if not create them

import os
import sys
import shutil
import threading
import xml.etree.ElementTree as ET
import tkinter as tk
from tkinter import ttk, filedialog


class ExtractData(threading.Thread):
    def __init__(self, sdRepoFolder, addStatus=None):
        self.sdRepoFolder = sdRepoFolder
        self.addStatus = addStatus

        if not os.path.isdir(self.sdRepoFolder) or not os.access(self.sdRepoFolder, os.R_OK):
            text = sys.argv[1] + " folder does not exist, is not a folder or does not have read permissions"
            if self.addStatus:
                self.addStatus(text)
            else:
                print(text, flush=True)
                exit(1)

        carMainFolder = self.sdRepoFolder + 'data/cars/models/'
        carCatMainFolder = self.sdRepoFolder + 'data/cars/categories/'

        trackMainFolder = self.sdRepoFolder + 'data/tracks/'
        trackCatMainFolder = self.sdRepoFolder + 'data/tracks/'

        self.root = None

        ##============================
        ## EXTRACT CARS CATEGORY DATA
        ##============================
        carCategories = {}

        catFiles = os.listdir(carCatMainFolder)

        for catFile in catFiles:
            xmlCatFile = carCatMainFolder+catFile

            fileName, fileExtension = os.path.splitext(xmlCatFile)

            if fileExtension == '.xml':
                if os.path.isfile(xmlCatFile):
                    xmlFileUrl = xmlCatFile
                    parser = ET.XMLParser()
                    tree = ET.parse(xmlFileUrl, parser=parser)

                    # And get the self.root of the xml tree
                    self.root = tree.getroot()

                    catName = self.root.attrib['name']
                    catId = self.getTagAttr('Car', attstr='category')['val']

                    carCategories[catId] = {}
                    carCategories[catId]['cars'] = []
                    carCategories[catId]['name'] = catName

                    #print 'Processed: '+catId+' : '+catName


        ##============================
        ## EXTRACT CARS DATA
        ##============================
        cars = {}
        carFolders = os.listdir(carMainFolder)

        for folder in carFolders:
            dirName = carMainFolder + folder + '/'

            xmlFileUrl = dirName+folder+'.xml'
            imgFileUrl = dirName+folder+'-preview.jpg'

            if os.path.isfile(xmlFileUrl):

                if os.path.isfile(imgFileUrl):
                    newImgUrl = './public/img/cars/'+folder+'-preview.jpg'
                    carImg = './img/cars/'+folder+'-preview.jpg'
                    shutil.copyfile(imgFileUrl, newImgUrl)

                tree = ET.parse(xmlFileUrl)

                # And get the self.root of the xml tree
                self.root = tree.getroot()

                # Car name
                carName = self.root.attrib['name']
                carId = folder
                # Car category
                carCategory = self.getTagAttr('Car', attstr='category')['val']
                carWidth = self.getTagAttr('Car', attnum='body length')['val']

                # print('Processing car: {} : {} : {}'.format(carId, carName, carWidth))
                text = 'Processing car: {} : {} : {}'.format(carId, carName, carWidth)
                if self.addStatus:
                    self.addStatus(text)
                else:
                    print(text, flush=True)

                # Assign the car to a car categorie
                carCategories[carCategory]['cars'].append(carId)

                # Populate the car object with all the infos of the car
                cars[carId] = {}
                cars[carId]['id'] = carId
                cars[carId]['name'] = carName
                cars[carId]['img'] = carImg
                cars[carId]['category'] = carCategory

                try:
                    overall_width = self.getTagAttr('Car', attnum='overall width')
                    cars[carId]['width'] = '{} {}'.format(
                        overall_width['unit'],
                        overall_width['val']
                    )
                except:
                    cars[carId]['width'] = "data unavailable"

                try:
                    overall_length = self.getTagAttr('Car', attnum='overall length')
                    cars[carId]['length'] = '{} {}'.format(
                        overall_length['unit'],
                        overall_length['val']
                    )
                except:
                    cars[carId]['lenght'] = "data unavailable"

                try:
                    overall_mass = self.getTagAttr('Car', attnum='mass')
                    cars[carId]['mass'] = '{} {}'.format(
                        overall_mass['unit'],
                        overall_mass['val']
                    )
                except:
                    cars[carId]['mass'] = "data unavailable"

                # mpa11 musarasama has problems (missing some data)
                try:
                    overall_fueltank = self.getTagAttr('Car', attnum='fuel tank')
                    cars[carId]['fueltank'] = '{} {}'.format(
                        overall_fueltank['unit'],
                        overall_fueltank['val']
                    )
                except:
                    cars[carId]['fueltank'] = "data unavailable"

                try:
                    cylinders = self.getTagAttr('Engine', attnum='cylinders')
                    shape = self.getTagAttr('Engine', attstr='shape')
                    capacity = self.getTagAttr('Engine', attnum='capacity')

                    cars[carId]['engine'] = '{} cylinders {} {} {}'.format(
                        cylinders['val'],
                        shape['val'],
                        capacity['val'],
                        capacity['unit']
                    )

                except:
                    cars[carId]['engine'] = "data unavailable"

                cars[carId]['drivetrain'] = self.getTagAttr('Drivetrain', attstr='type')['val']
                text = 'Processed car: {} : {}'.format(carId, carName)
                if self.addStatus:
                    self.addStatus(text)
                else:
                    print(text, flush=True)
                
                #self.addStatus(text)

        ##============================
        ## EXTRACT TRACKS CATEGORY DATA
        ##============================
        trackCategories = {}

        ##============================
        ## EXTRACT TRACKS DATA
        ##============================
        tracks = {}
        trackCategoryFolders = os.listdir(trackMainFolder)

        for category in trackCategoryFolders:
            categoryFolder = trackMainFolder + category+'/'

            if not os.path.isfile(trackMainFolder + category):

                categoryFolders = os.listdir(categoryFolder)

                # Log car categories info
                trackCategories[category] = {}
                trackCategories[category]['id'] = category
                trackCategories[category]['name'] = category
                trackCategories[category]['tracks'] = []

                for track in categoryFolders:
                    #print categoryFolder+'\n'
                    #print track+'\n\n'

                    if not os.path.isfile(categoryFolder+track):

                        trackFolder = categoryFolder+track+'/'
                        xmlFileUrl = trackFolder+track+'.xml'

                        #print categoryFolder+track

                        if not os.path.isfile(categoryFolder+track):
                            if os.path.isfile(xmlFileUrl):
                                #print xmlFileUrl

                                parser = ET.XMLParser()
                                #parser._parser.UseForeignDTD(True)
                                parser.entity['default-surfaces'] = u'\u00A0'
                                parser.entity['default-objects'] = u'\u00A0'
                                tree = ET.parse(xmlFileUrl, parser=parser)

                                # And get the self.root of the xml tree
                                self.root = tree.getroot()

                                #trackId=self.root.attrib['name']
                                trackId = track
                                trackName = self.getTagAttr('Header', attstr='name')['val']
                                trackCategory = self.getTagAttr('Header', attstr='category')['val']
                                imgFileUrl = trackFolder+'outline.png'

                                # We want to ignore development tracks
                                if (trackCategory == "development"):
                                    #print('INFO: Ignoring track as is a development one for: ' + trackId + ' : ' + trackName)
                                    text = 'INFO: Ignoring track as is a development one for: {} : {}'.format(trackId, trackName)
                                    if self.addStatus:
                                        self.addStatus(text)
                                    else:
                                        print(text, flush=True)
                                    continue

                                if os.path.isfile(imgFileUrl):
                                    newImgUrl = './public/img/tracks/' + track + '-outline.png'
                                    trackImg = './img/tracks/' + track + '-outline.png'
                                    shutil.copyfile(imgFileUrl, newImgUrl)
                                else:
                                    '''print('WARNING: No track image defined for: {} : {}'.format(
                                        trackId,
                                        trackName
                                    ))'''
                                    text = 'WARNING: No track image defined for: {} : {}'.format(
                                        trackId,
                                        trackName
                                    )
                                    
                                    if self.addStatus:
                                        self.addStatus(text)
                                    else:
                                        print(text, flush=True)
                                    trackImg = ''

                                # Populate the car object with all the infos of the track
                                tracks[trackId] = {}
                                tracks[trackId]['id'] = trackId
                                tracks[trackId]['name'] = trackName
                                tracks[trackId]['img'] = trackImg
                                tracks[trackId]['category'] = trackCategory

                                tracks[trackId]['author'] = self.getTagAttr('Header', attstr='author')['val']
                                temp = self.getTagAttr('Header', attstr='description')['val'].replace("'", "*")
                                tracks[trackId]['description'] = temp
                                #tracks[trackId]['version']=self.root.findall("./section[@name='Header']/attstr[@name='version']")[0].attrib['val']

                                trackCategories[category]['tracks'].append(trackId)

                                text = 'Processed track: {} : {}'.format(trackId, trackName)
                                if self.addStatus:
                                    self.addStatus(text)
                                else:
                                    print(text, flush=True)


        # Save he carCategory info into a file
        out_file = open("./writable/data/carCategories.txt", "w")
        out_file.write(str(carCategories))
        out_file.close()

        # Save the cars info into a file
        out_file = open("./writable/data/cars.txt", "w")
        out_file.write(str(cars))
        out_file.close()

        # Save the carTrack info into a file
        out_file = open("./writable/data/trackCategories.txt", "w")
        out_file.write(str(trackCategories))
        out_file.close()

        # Save the tracks info into a file
        out_file = open("./writable/data/tracks.txt", "w")
        out_file.write(str(tracks))
        out_file.close()

    def getTagAttr(self, section, attstr=None, attnum=None):
            """
            Find a attstr or attnum tag and return he attributes.
            Args:
                section (str): The section to find
                attstr (str, optional): If find a attstr tag. Defaults to None.
                attnum (str, optional): If find a attnum tag. Defaults to None.
            """
            attribs = None
            element = None

            if attstr:
                element = self.root.findall(
                    "./section[@name='{}']/attstr[@name='{}']".format(
                        section,
                        attstr
                    )
                )

            elif attnum:
                element = self.root.findall(
                    "./section[@name='{}']/attnum[@name='{}']".format(
                        section,
                        attnum
                    )
                )

            if element and len(element) > 0:
                attribs = element[0].attrib

            return attribs


class Gui(tk.Frame):
    def __init__(self, parent, gui=True, sdRepoFolder=None):
        super().__init__(parent)
        self.root = None
        self.gui = gui
        self.sdRepoFolder = sdRepoFolder
        self.status = None

        if self.gui:
            top = self.winfo_toplevel()
            top.rowconfigure(1, weight=1)
            top.columnconfigure(2, weight=1)

            self.entryFolder = tk.Entry(width=50)
            self.entryFolder.grid(column=0, row=0, sticky='e')
            self.openBtn = tk.Button(text='Open folder', command=self.selDataDir)
            self.openBtn.grid(column=1, row=0, sticky='w')

            frame = tk.Frame()

            scroll_bar = ttk.Scrollbar(frame, orient=tk.VERTICAL)

            self.status = tk.Listbox(frame, yscrollcommand=scroll_bar.set)
            self.status.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)

            scroll_bar.configure(command=self.status.yview)
            scroll_bar.pack(side=tk.RIGHT, fill=tk.Y)

            frame.rowconfigure('all', weight=1)
            frame.columnconfigure('all', weight=1)
            frame.grid(column=0, row=1, columnspan=3, sticky="nsew")

            self.startBtn = tk.Button(
                text='Extract',
                command=self.startThread,
                state=tk.DISABLED)
            self.startBtn.grid(column=2, row=0, sticky='w')
        else:
            self.startThread()

    def selDataDir(self):
        self.sdRepoFolder = filedialog.askdirectory() + '/'
        if self.sdRepoFolder:
            self.entryFolder.insert(0, self.sdRepoFolder)
            self.startBtn['state'] = tk.NORMAL

    def startThread(self):
        threading.Thread(
            target=ExtractData,
            args=(self.sdRepoFolder, self.addStatus)
        ).start()

    def addStatus(self, text):
        if (self.gui):
            self.status.insert(tk.END, text)
        else:
            print(text)


if len(sys.argv) == 2:
    sdRepoFolder = sys.argv[1]
    threading.Thread(
        target=ExtractData,
        args=(sdRepoFolder,)
    ).start()
else:
    window = tk.Tk()
    window.title("Speed Dreams: Extract Data from SVN")
    window.geometry('640x480')
    app = Gui(window)
    window.mainloop()
