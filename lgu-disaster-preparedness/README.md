# LGU Disaster Preparedness Training and Simulation

This project is designed to provide a comprehensive system for disaster preparedness training and simulation for Local Government Units (LGUs). It includes both backend API functionalities and a frontend user interface.

## Project Structure

The project is organized into several key directories and files:

- **/api**: Contains all API-related scripts.
  - **/action**: Scripts for handling specific actions related to disaster preparedness.
  - **/retrieve**: Scripts for retrieving data related to training sessions or simulations.
  - **config.php**: Configuration settings for the API, including database connection details.
  - **test.php**: Testing scripts for API endpoints.

- **/frontend**: Contains the frontend application files.
  - **index.php**: The main entry point for the frontend, displaying the user interface.

- **/dummy**: A guide for code input, mirroring the structure of the main application.
  - **/api**: Contains dummy versions of the API scripts.
  - **/frontend**: Contains a dummy version of the frontend index file.

- **.env**: Environment variables for the application, including sensitive configuration settings.
- **.env.example**: An example of the .env file, showing required environment variables without sensitive data.
- **.gitignore**: Specifies files and directories to be ignored by Git.
- **db.sql**: SQL scripts for setting up the database schema and initial data.

## Setup Instructions

1. **Clone the Repository**: 
   Clone this repository to your local machine using:
   ```
   git clone <repository-url>
   ```

2. **Install Dependencies**: 
   Navigate to the project directory and install any necessary dependencies.

3. **Configure Environment Variables**: 
   Copy the `.env.example` file to `.env` and fill in the required environment variables.

4. **Set Up the Database**: 
   Run the SQL scripts in `db.sql` to set up the database schema and initial data.

5. **Run the Application**: 
   Start the server and access the application through the frontend interface.

## Usage Guidelines

- Use the API endpoints defined in the `/api` directory for backend functionalities.
- The frontend interface can be accessed via the `index.php` file in the `/frontend` directory.
- Refer to the `/dummy` directory for guidance on how to structure your code and implement features.

## Contributing

Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License

This project is licensed under the MIT License. See the LICENSE file for more details.