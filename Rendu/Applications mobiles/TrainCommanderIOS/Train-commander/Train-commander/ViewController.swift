//
//  ViewController.swift
//  Train-commander
//
//  Created by Supinfo on 25/05/2016.
//  Copyright © 2016 Supinfo. All rights reserved.
//

import UIKit

class ViewController: UIViewController {
    @IBOutlet weak var webView: UIWebView!

    override func viewDidLoad() {
        super.viewDidLoad()
        let url = NSURL (string: "https://www.train-commander.fr/");
        let requestObj = NSURLRequest(URL: url!);
        webView.loadRequest(requestObj);
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }


}


