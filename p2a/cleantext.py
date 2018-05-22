#!/usr/bin/env python

"""Clean comment text for easier parsing."""

from __future__ import print_function

import re
import sys
import json
import string
import argparse


__author__ = ""
__email__ = ""

# Some useful data.
_CONTRACTIONS = {
    "tis": "'tis",
    "aint": "ain't",
    "amnt": "amn't",
    "arent": "aren't",
    "cant": "can't",
    "couldve": "could've",
    "couldnt": "couldn't",
    "didnt": "didn't",
    "doesnt": "doesn't",
    "dont": "don't",
    "hadnt": "hadn't",
    "hasnt": "hasn't",
    "havent": "haven't",
    "hed": "he'd",
    "hell": "he'll",
    "hes": "he's",
    "howd": "how'd",
    "howll": "how'll",
    "hows": "how's",
    "id": "i'd",
    "ill": "i'll",
    "im": "i'm",
    "ive": "i've",
    "isnt": "isn't",
    "itd": "it'd",
    "itll": "it'll",
    "its": "it's",
    "mightnt": "mightn't",
    "mightve": "might've",
    "mustnt": "mustn't",
    "mustve": "must've",
    "neednt": "needn't",
    "oclock": "o'clock",
    "ol": "'ol",
    "oughtnt": "oughtn't",
    "shant": "shan't",
    "shed": "she'd",
    "shell": "she'll",
    "shes": "she's",
    "shouldve": "should've",
    "shouldnt": "shouldn't",
    "somebodys": "somebody's",
    "someones": "someone's",
    "somethings": "something's",
    "thatll": "that'll",
    "thats": "that's",
    "thatd": "that'd",
    "thered": "there'd",
    "therere": "there're",
    "theres": "there's",
    "theyd": "they'd",
    "theyll": "they'll",
    "theyre": "they're",
    "theyve": "they've",
    "wasnt": "wasn't",
    "wed": "we'd",
    "wedve": "wed've",
    "well": "we'll",
    "were": "we're",
    "weve": "we've",
    "werent": "weren't",
    "whatd": "what'd",
    "whatll": "what'll",
    "whatre": "what're",
    "whats": "what's",
    "whatve": "what've",
    "whens": "when's",
    "whered": "where'd",
    "wheres": "where's",
    "whereve": "where've",
    "whod": "who'd",
    "whodve": "whod've",
    "wholl": "who'll",
    "whore": "who're",
    "whos": "who's",
    "whove": "who've",
    "whyd": "why'd",
    "whyre": "why're",
    "whys": "why's",
    "wont": "won't",
    "wouldve": "would've",
    "wouldnt": "wouldn't",
    "yall": "y'all",
    "youd": "you'd",
    "youll": "you'll",
    "youre": "you're",
    "youve": "you've"
}

# You may need to write regular expressions.

# https://stackoverflow.com/questions/367155/splitting-a-string-into-words-and-punctuation

def sanitize(text):
    """Do parse the text in variable "text" according to the spec, and return
    a LIST containing FOUR strings 
    1. The parsed text.
    2. The unigrams
    3. The bigrams
    4. The trigrams
    """

    # YOUR CODE GOES BELOW:

    text = re.sub(r'[\n\t]', ' ', text)
    no_url = re.sub(r'https?:\/\/\S+', '', text)
    split = no_url.split()
    punctuated = []
    for token in split:
        token = token.lower()
        temp = re.findall(r"[\w']+|[.,!?;:]", token)
        punctuated += temp

    parsed_text = ""
    not_punctuated = []
    
    if len(punctuated) > 0:
        parsed_text = punctuated[0]
        for i in range(1,len(punctuated)):
            temp = " " + punctuated[i]
            parsed_text += temp

        for token in punctuated:
            if token not in {'.', ',', '!', '?', ';', ':'}:
                not_punctuated.append(token)

    unigrams = ""
    bigrams = ""
    trigrams = ""

    if len(not_punctuated) > 0:
        unigrams = not_punctuated[0]
        for i in range(1,len(not_punctuated)):
            temp = " " + not_punctuated[i]
            unigrams += temp

    if len(not_punctuated) > 1:
        bigrams = not_punctuated[0] + "_" + not_punctuated[1]
        for i in range(2,len(not_punctuated)):
            temp = " " + not_punctuated[i-1] + "_" + not_punctuated[i]
            bigrams += temp

    if len(not_punctuated) > 2:
        trigrams = not_punctuated[0] + "_" + not_punctuated[1] + "_" + not_punctuated[2]
        for i in range(3,len(not_punctuated)):
            temp = " " + not_punctuated[i-2] + "_" + not_punctuated[i-1] + "_" + not_punctuated[i]
            trigrams += temp

    return [parsed_text, unigrams, bigrams, trigrams]


if __name__ == "__main__":
    # This is the Python main function.
    # You should be able to run
    # python cleantext.py <filename>
    # and this "main" function will open the file,
    # read it line by line, extract the proper value from the JSON,
    # pass to "sanitize" and print the result as a list.

    # YOUR CODE GOES BELOW.

    filename = sys.argv[1]
    file = open(filename, "r")
    for line in file:
        parsed_json = json.loads(line)
        print(sanitize(parsed_json["body"]))

