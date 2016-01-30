# Get the directory that this configuration file exists in
dir = File.dirname(__FILE__)

# Load the sencha-touch framework automatically.
#load File.join(dir, 'stylesheets' )

# Compass configurations
sass_path    = dir
css_path     = File.join(dir,"..", "..", "resources","css")
environment  = :production
# :compressed or :expanded
output_style = :expanded